<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class CustomerHomeProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_home_shows_real_scoped_summary_and_recent_orders(): void
    {
        $customer = Customer::factory()->create();
        $other = Customer::factory()->create();
        Order::factory()->for($customer)->create(['title' => 'طلبي الحديث', 'status' => 'pending']);
        Order::factory()->for($customer)->create(['title' => 'طلب مكتمل', 'status' => 'completed']);
        Order::factory()->for($other)->create(['title' => 'طلب عميل آخر']);

        $this->actingAs($customer->user, 'customer')
            ->get('/herafy/main')
            ->assertOk()
            ->assertSee($customer->user->name)
            ->assertSee('طلب خدمة جديد')
            ->assertSee('طلبي الحديث')
            ->assertSee('طلب مكتمل')
            ->assertDontSee('طلب عميل آخر')
            ->assertSee('>1<', false);
    }

    public function test_customer_home_has_a_clean_empty_state(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->get('/herafy/main')
            ->assertOk()
            ->assertSee('لا توجد طلبات بعد')
            ->assertSee(route('request'), false);
    }

    public function test_customer_can_view_read_only_profile_and_open_edit_page(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.customer.profile.show'))
            ->assertOk()
            ->assertSee($customer->user->email)
            ->assertSee('تعديل الملف الشخصي')
            ->assertSee('href="'.route('herafy.customer.profile.edit').'"', false)
            ->assertDontSee('action="'.route('herafy.customer.profile.update').'"', false)
            ->assertDontSee('name="profile_photo"', false)
            ->assertDontSee('name="actor_type"', false)
            ->assertDontSee('name="status"', false);

        $this->get(route('herafy.customer.profile.edit'))
            ->assertOk()
            ->assertSee('name="profile_photo"', false)
            ->assertSee('name="name"', false)
            ->assertSee('name="user_name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="mobile"', false)
            ->assertSee('name="address"', false)
            ->assertSee('name="gender"', false)
            ->assertSee('حفظ التعديلات');

        auth('customer')->logout();
        $this->get(route('herafy.customer.profile.show'))->assertRedirect(route('login'));
        $this->get(route('herafy.customer.profile.edit'))->assertRedirect(route('login'));

        $craftman = Craftman::factory()->create();
        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.customer.profile.show'))
            ->assertRedirect(route('login'));
        $this->get(route('herafy.customer.profile.edit'))->assertRedirect(route('login'));
    }

    public function test_customer_updates_basic_fields_without_changing_trusted_identity_fields(): void
    {
        $customer = Customer::factory()->create();
        $user = $customer->user;
        $originalActorType = $user->actor_type;
        $originalActorId = $user->actor_id;

        $this->actingAs($user, 'customer')
            ->patch(route('herafy.customer.profile.update'), $this->validProfilePayload([
                'name' => 'الاسم الجديد',
                'actor_type' => Craftman::class,
                'actor_id' => 99999,
                'status' => 'blocked',
            ]))
            ->assertRedirect(route('herafy.customer.profile.show'));

        $user->refresh();
        $this->assertSame('الاسم الجديد', $user->name);
        $this->assertSame($originalActorType, $user->actor_type);
        $this->assertSame($originalActorId, $user->actor_id);
        $this->assertSame('active', $user->status);
        $this->assertTrue($user->isCustomer());
    }

    public function test_profile_email_and_username_are_unique_except_for_current_user(): void
    {
        $customer = Customer::factory()->create();
        $existing = User::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.profile.update'), $this->validProfilePayload([
                'email' => $existing->email,
                'user_name' => $existing->user_name,
            ]))
            ->assertSessionHasErrors(['email', 'user_name']);

        $this->patch(route('herafy.customer.profile.update'), $this->validProfilePayload())
            ->assertRedirect(route('herafy.customer.profile.show'));
    }

    public function test_customer_uploads_photo_and_navbar_displays_it(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.profile.update'), [
                ...$this->validProfilePayload(),
                '_method' => 'PATCH',
                'profile_photo' => $this->fakePng('avatar.png'),
            ])
            ->assertRedirect(route('herafy.customer.profile.show'));

        $path = $customer->user->fresh()->image;
        $this->assertStringStartsWith("profile-photos/customers/{$customer->id}/", $path);
        Storage::disk('public')->assertExists($path);

        $this->get(route('index'))->assertSee(Storage::disk('public')->url($path), false);
    }

    public function test_replacing_photo_deletes_old_file_after_success(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();
        $oldPath = "profile-photos/customers/{$customer->id}/old.jpg";
        Storage::disk('public')->put($oldPath, 'old');
        $customer->user->update(['image' => $oldPath]);

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.profile.update'), [
                ...$this->validProfilePayload(),
                '_method' => 'PATCH',
                'profile_photo' => $this->fakePng('new.png'),
            ])
            ->assertRedirect(route('herafy.customer.profile.show'));

        $newPath = $customer->user->fresh()->image;
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_failed_profile_update_removes_new_photo_and_preserves_record(): void
    {
        Storage::fake('public');
        $customer = Customer::factory()->create();
        $originalName = $customer->user->name;

        User::updating(function (): void {
            throw new RuntimeException('Forced profile update failure.');
        });

        $this->actingAs($customer->user, 'customer')
            ->from(route('herafy.customer.profile.edit'))
            ->post(route('herafy.customer.profile.update'), [
                ...$this->validProfilePayload(['name' => 'Should Roll Back']),
                '_method' => 'PATCH',
                'profile_photo' => $this->fakePng('orphan.png'),
            ])
            ->assertRedirect(route('herafy.customer.profile.edit'))
            ->assertSessionHas('error');

        $this->assertSame($originalName, $customer->user->fresh()->name);
        $this->assertNull($customer->user->fresh()->image);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    private function validProfilePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Updated Customer',
            'user_name' => 'updated_customer',
            'email' => 'updated.customer@example.test',
            'mobile' => '0599999999',
            'address' => 'Gaza',
            'gender' => 'male',
        ], $overrides);
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true)
        );
    }
}
