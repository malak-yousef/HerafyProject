<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\Review;
use App\Models\ServiceJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CraftmanDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_authenticated_craftman_can_access_dashboard(): void
    {
        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee($craftman->user->name)
            ->assertSee('تصفح الطلبات المتاحة')
            ->assertSee('عروضي')
            ->assertSee('أعمالي');

        auth('craftman')->logout();
        $this->get(route('herafy.craftman.dashboard'))->assertRedirect(route('login'));

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.craftman.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_counts_and_activity_are_scoped_to_craftman(): void
    {
        $profession = Profession::factory()->create();
        $otherProfession = Profession::factory()->create();
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $otherCraftman = Craftman::factory()->create(['profession_id' => $profession->id]);

        Order::factory()->create(['profession_id' => $profession->id, 'status' => 'pending', 'title' => 'طلب متاح']);
        Order::factory()->create(['profession_id' => $otherProfession->id, 'status' => 'pending', 'title' => 'تخصص آخر']);
        Order::factory()->create(['profession_id' => $profession->id, 'status' => 'accepted', 'title' => 'طلب مغلق']);

        $ownOfferOrder = Order::factory()->create(['title' => 'طلب عرضي الخاص']);
        Offer::factory()->create(['craftman_id' => $craftman->id, 'order_id' => $ownOfferOrder->id, 'status' => 'pending']);
        Offer::factory()->create(['craftman_id' => $craftman->id, 'status' => 'accepted']);
        $otherOfferOrder = Order::factory()->create(['title' => 'طلب عرض حرفي آخر']);
        Offer::factory()->create(['craftman_id' => $otherCraftman->id, 'order_id' => $otherOfferOrder->id, 'status' => 'pending']);

        $this->createJob($craftman, 'pending');
        $this->createJob($craftman, 'in_progress');
        $this->createJob($craftman, 'completed');
        $this->createJob($otherCraftman, 'pending');

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee('data-testid="available-orders-count">1<', false)
            ->assertSee('data-testid="pending-offers-count">1<', false)
            ->assertSee('data-testid="active-jobs-count">2<', false)
            ->assertSee('data-testid="completed-jobs-count">1<', false)
            ->assertSee('طلب عرضي الخاص')
            ->assertDontSee('طلب عرض حرفي آخر');
    }

    public function test_dashboard_displays_empty_activity_states(): void
    {
        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee('لم تقدم أي عروض بعد')
            ->assertSee('لا توجد أعمال مسندة إليك بعد');
    }

    public function test_craftman_views_only_their_real_profile_and_approved_ratings(): void
    {
        $profession = Profession::factory()->create(['name' => 'نجارة مخصصة']);
        $craftman = Craftman::factory()->create([
            'profession_id' => $profession->id,
            'description' => 'نبذة الحرفي الخاصة',
            'experience_years' => 9,
        ]);
        $otherCraftman = Craftman::factory()->create(['description' => 'بيانات حرفي آخر']);
        $this->createReview($craftman, 5, 'تقييم معتمد أول', 'approved');
        $this->createReview($craftman, 3, 'تقييم معتمد ثان', 'approved');
        $this->createReview($craftman, 1, 'تقييم معلق', 'pending');
        $this->createReview($otherCraftman, 5, 'تقييم حرفي آخر', 'approved');

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.craftman.profile.show'))
            ->assertOk()
            ->assertSee($craftman->user->name)
            ->assertSee('نجارة مخصصة')
            ->assertSee('نبذة الحرفي الخاصة')
            ->assertSee('4.0')
            ->assertSee('(2 تقييم)')
            ->assertSee('تقييم معتمد أول')
            ->assertSee('تقييم معتمد ثان')
            ->assertDontSee('تقييم معلق')
            ->assertDontSee('بيانات حرفي آخر')
            ->assertDontSee('تقييم حرفي آخر');
    }

    public function test_guest_and_customer_cannot_access_craftman_profile(): void
    {
        $this->get(route('herafy.craftman.profile.show'))->assertRedirect(route('login'));

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.craftman.profile.show'))
            ->assertRedirect(route('login'));
    }

    public function test_craftman_can_edit_only_safe_profile_fields(): void
    {
        $profession = Profession::factory()->create(['status' => 'active']);
        $craftman = Craftman::factory()->create();
        $originalActorId = $craftman->user->actor_id;

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.profile.update'), [
                'name' => 'حرفي محدّث',
                'user_name' => 'updated-craftman',
                'email' => 'updated-craftman@example.com',
                'mobile' => '0599999999',
                'address' => 'غزة',
                'gender' => 'male',
                'profession_id' => $profession->id,
                'experience_years' => 12,
                'hourly_rate' => 45,
                'daily_rate' => 250,
                'description' => 'وصف مهني محدث',
                'actor_type' => Customer::class,
                'actor_id' => 99999,
                'status' => 'in_active',
                'rating' => 5,
            ])
            ->assertRedirect(route('herafy.craftman.profile.show'));

        $craftman->refresh();
        $craftman->user->refresh();
        $this->assertSame('حرفي محدّث', $craftman->user->name);
        $this->assertSame($originalActorId, $craftman->user->actor_id);
        $this->assertSame(Craftman::class, $craftman->user->actor_type);
        $this->assertSame('active', $craftman->user->status);
        $this->assertSame($profession->id, $craftman->profession_id);
        $this->assertSame('وصف مهني محدث', $craftman->description);
    }

    public function test_craftman_profile_photo_upload_and_replacement_are_safe(): void
    {
        Storage::fake('public');
        $profession = Profession::factory()->create(['status' => 'active']);
        $craftman = Craftman::factory()->create();

        $payload = $this->profilePayload($craftman, $profession);
        $payload['profile_photo'] = $this->fakePng('first.png');
        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.profile.update'), $payload)
            ->assertRedirect(route('herafy.craftman.profile.show'));

        $firstPath = $craftman->user->fresh()->image;
        Storage::disk('public')->assertExists($firstPath);

        $payload['profile_photo'] = $this->fakePng('second.png');
        $this->patch(route('herafy.craftman.profile.update'), $payload)->assertRedirect();
        $secondPath = $craftman->user->fresh()->image;
        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);

        $this->get(route('herafy.craftman.dashboard'))->assertSee(Storage::disk('public')->url($secondPath));
    }

    public function test_guest_and_customer_cannot_edit_craftman_profile(): void
    {
        $this->get(route('herafy.craftman.profile.edit'))->assertRedirect(route('login'));

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.craftman.profile.edit'))
            ->assertRedirect(route('login'));
    }

    private function profilePayload(Craftman $craftman, Profession $profession): array
    {
        return [
            'name' => $craftman->user->name,
            'user_name' => $craftman->user->user_name,
            'email' => $craftman->user->email,
            'mobile' => $craftman->user->mobile,
            'address' => $craftman->user->address,
            'gender' => $craftman->user->gender,
            'profession_id' => $profession->id,
            'experience_years' => $craftman->experience_years,
            'hourly_rate' => $craftman->hourly_rate,
            'daily_rate' => $craftman->daily_rate,
            'description' => $craftman->description,
        ];
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true)
        );
    }

    private function createJob(Craftman $craftman, string $status): ServiceJob
    {
        $offerStatus = match ($status) {
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            default => 'accepted',
        };
        $offer = Offer::factory()->create([
            'craftman_id' => $craftman->id,
            'status' => $offerStatus,
        ]);

        return ServiceJob::factory()->create([
            'offer_id' => $offer->id,
            'order_id' => $offer->order_id,
            'customer_id' => $offer->order->customer_id,
            'craftman_id' => $craftman->id,
            'status' => $status,
        ]);
    }

    private function createReview(Craftman $craftman, int $rating, string $comment, string $status): Review
    {
        $order = Order::factory()->create();

        return Review::create([
            'craftsman_id' => $craftman->id,
            'customer_id' => $order->customer_id,
            'request_id' => $order->id,
            'rating' => $rating,
            'comment' => $comment,
            'status' => $status,
        ]);
    }
}
