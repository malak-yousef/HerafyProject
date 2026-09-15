<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Profession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_photo_upload_stores_file_and_relative_database_path(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.profile.update'), $this->customerPayload($customer, $this->photo('customer.png')))
            ->assertRedirect(route('herafy.customer.profile.show'));

        $path = $customer->user->fresh()->image;
        $this->assertStringStartsWith("profile-photos/customers/{$customer->id}/", $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_customer_profile_renders_public_storage_asset_url(): void
    {
        $customer = Customer::factory()->create();
        $path = "profile-photos/customers/{$customer->id}/customer.png";
        $customer->user->update(['image' => $path]);

        $this->actingAs($customer->user, 'customer')->get(route('herafy.customer.profile.show'))
            ->assertOk()->assertSee(asset('storage/'.$path), false);
    }

    public function test_customer_navbar_renders_photo(): void
    {
        $customer = Customer::factory()->create();
        $path = "profile-photos/customers/{$customer->id}/navbar.png";
        $customer->user->update(['image' => $path]);

        $this->actingAs($customer->user, 'customer')->get(route('main'))
            ->assertOk()->assertSee(asset('storage/'.$path), false);
    }

    public function test_craftman_photo_upload_stores_file_and_relative_database_path(): void
    {
        Storage::fake('public');
        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.profile.update'), $this->craftmanPayload($craftman, $this->photo('craftman.png')))
            ->assertRedirect(route('herafy.craftman.profile.show'));

        $path = $craftman->user->fresh()->image;
        $this->assertStringStartsWith("profile-photos/craftmen/{$craftman->id}/", $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_craftman_profile_renders_public_storage_asset_url(): void
    {
        $craftman = Craftman::factory()->create();
        $path = "profile-photos/craftmen/{$craftman->id}/craftman.png";
        $craftman->user->update(['image' => $path]);

        $this->actingAs($craftman->user, 'craftman')->get(route('herafy.craftman.profile.show'))
            ->assertOk()->assertSee(asset('storage/'.$path), false);
    }

    public function test_craftman_navbar_renders_photo(): void
    {
        $craftman = Craftman::factory()->create();
        $path = "profile-photos/craftmen/{$craftman->id}/navbar.png";
        $craftman->user->update(['image' => $path]);

        $this->actingAs($craftman->user, 'craftman')->get(route('herafy.craftman.dashboard'))
            ->assertOk()->assertSee(asset('storage/'.$path), false);
    }

    public function test_profile_and_navbar_keep_fallback_avatar_without_photo(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')->get(route('herafy.customer.profile.show'))
            ->assertOk()->assertSee('profile-avatar-fallback', false)->assertSee('<i class="fas fa-user"></i>', false);
    }

    public function test_replacing_customer_and_craftman_photos_still_works(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();
        $craftman = Craftman::factory()->create();
        $customerOld = "profile-photos/customers/{$customer->id}/old.png";
        $craftmanOld = "profile-photos/craftmen/{$craftman->id}/old.png";
        Storage::disk('public')->put($customerOld, 'old');
        Storage::disk('public')->put($craftmanOld, 'old');
        $customer->user->update(['image' => $customerOld]);
        $craftman->user->update(['image' => $craftmanOld]);

        $this->actingAs($customer->user, 'customer')->patch(
            route('herafy.customer.profile.update'),
            $this->customerPayload($customer, $this->photo('new-customer.png')),
        )->assertRedirect();
        auth('customer')->logout();
        $this->actingAs($craftman->user, 'craftman')->patch(
            route('herafy.craftman.profile.update'),
            $this->craftmanPayload($craftman, $this->photo('new-craftman.png')),
        )->assertRedirect();

        Storage::disk('public')->assertMissing($customerOld);
        Storage::disk('public')->assertMissing($craftmanOld);
        Storage::disk('public')->assertExists($customer->user->fresh()->image);
        Storage::disk('public')->assertExists($craftman->user->fresh()->image);
    }

    public function test_current_and_legacy_photo_path_formats_render_consistently(): void
    {
        $customer = Customer::factory()->create();
        $craftman = Craftman::factory()->create();

        $customer->user->update(['image' => 'legacy-customer.png']);
        $craftman->user->update(['image' => 'C:\\xampp\\htdocs\\herafy\\storage\\app\\public\\profile-photos\\craftmen\\1\\legacy.png']);

        $this->assertSame('images/customer/legacy-customer.png', $customer->user->fresh()->profilePhotoPath());
        $this->assertSame('profile-photos/craftmen/1/legacy.png', $craftman->user->fresh()->profilePhotoPath());
        $this->assertSame(asset('storage/images/customer/legacy-customer.png'), $customer->user->profilePhotoUrl());
        $this->assertSame(asset('storage/profile-photos/craftmen/1/legacy.png'), $craftman->user->profilePhotoUrl());
    }

    private function customerPayload(Customer $customer, UploadedFile $photo): array
    {
        $user = $customer->user;

        return ['name' => $user->name, 'user_name' => $user->user_name, 'email' => $user->email,
            'mobile' => $user->mobile, 'address' => $user->address, 'gender' => $user->gender, 'profile_photo' => $photo];
    }

    private function craftmanPayload(Craftman $craftman, UploadedFile $photo): array
    {
        $profession = Profession::query()->find($craftman->profession_id);
        $profession->update(['status' => 'active']);
        $user = $craftman->user;

        return ['name' => $user->name, 'user_name' => $user->user_name, 'email' => $user->email,
            'mobile' => $user->mobile, 'address' => $user->address, 'gender' => $user->gender,
            'profession_id' => $profession->id, 'experience_years' => $craftman->experience_years,
            'hourly_rate' => $craftman->hourly_rate, 'daily_rate' => $craftman->daily_rate,
            'description' => $craftman->description, 'profile_photo' => $photo];
    }

    private function photo(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true
        ));
    }
}
