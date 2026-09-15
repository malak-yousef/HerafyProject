<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\Report;
use App\Models\Review;
use App\Models\ServiceJob;
use App\Models\User;
use App\Models\Verification;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\HerafyDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactorySeederFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_and_actor_factories_create_valid_classifications(): void
    {
        $admin = User::factory()->admin()->create();
        $customerUser = User::factory()->customer()->create();
        $craftmanUser = User::factory()->craftman()->create();
        $customer = Customer::factory()->inactive()->create();
        $craftman = Craftman::factory()->blocked()->create();

        $this->assertNull($admin->actor_type);
        $this->assertNull($admin->actor_id);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasRole('cms-admin', 'admin'));
        $this->assertTrue($customerUser->refresh()->isCustomer());
        $this->assertSame(Customer::class, $customerUser->actor_type);
        $this->assertTrue($craftmanUser->refresh()->isCraftman());
        $this->assertSame(Craftman::class, $craftmanUser->actor_type);
        $this->assertSame('inactive', $customer->user->status);
        $this->assertSame('blocked', $craftman->user->status);
        $this->assertNotNull($craftman->profession);
        $this->assertSame(0, $craftman->rating);
        $this->assertSame(0, $craftman->total_reviews);
    }

    public function test_order_and_offer_factories_preserve_location_and_profession_relationships(): void
    {
        $order = Order::factory()->create();
        $offer = Offer::factory()->create();

        $this->assertSame($order->city_id, $order->area->city_id);
        $this->assertTrue($order->customer->user->isCustomer());
        $this->assertSame('pending', $order->status);
        $this->assertSame($offer->order->profession_id, $offer->craftman->profession_id);
        $this->assertSame('pending', $offer->status);
        $this->assertTrue($offer->craftman->user->isCraftman());
    }

    public function test_service_job_states_are_mutually_consistent(): void
    {
        $pending = ServiceJob::factory()->pending()->create();
        $inProgress = ServiceJob::factory()->inProgress()->create();
        $completed = ServiceJob::factory()->completed()->create();
        $cancelled = ServiceJob::factory()->cancelled()->create();

        $this->assertJobConsistency($pending, 'accepted');
        $this->assertNull($pending->started_at);
        $this->assertNull($pending->completed_at);

        $this->assertJobConsistency($inProgress, 'in_progress');
        $this->assertNotNull($inProgress->started_at);
        $this->assertNull($inProgress->completed_at);

        $this->assertJobConsistency($completed, 'completed');
        $this->assertNotNull($completed->started_at);
        $this->assertNotNull($completed->completed_at);
        $this->assertTrue($completed->completed_at->greaterThanOrEqualTo($completed->started_at));

        $this->assertJobConsistency($cancelled, 'cancelled');
        $this->assertNull($cancelled->completed_at);
    }

    public function test_review_verification_and_report_factories_have_valid_states_and_links(): void
    {
        $reviews = collect([
            Review::factory()->pending()->create(),
            Review::factory()->approved()->create(),
            Review::factory()->rejected()->create(),
        ]);

        foreach ($reviews as $review) {
            $job = $review->request->serviceJob;
            $this->assertSame('completed', $review->request->status);
            $this->assertSame('completed', $job->status);
            $this->assertSame('completed', $job->offer->status);
            $this->assertSame($job->customer_id, $review->customer_id);
            $this->assertSame($job->craftman_id, $review->craftsman_id);
            $this->assertBetween(1, 5, $review->rating);
        }
        $this->assertSame(3, $reviews->pluck('request_id')->unique()->count());

        foreach (['pending', 'approved', 'rejected'] as $state) {
            $verification = Verification::factory()->{$state}()->create();
            $this->assertSame($state, $verification->status);
            $this->assertTrue($verification->craftman->user->isCraftman());
        }

        foreach (['pending', 'reviewing', 'resolved', 'rejected'] as $state) {
            $report = Report::factory()->{$state}()->create();
            $this->assertSame($state, $report->status);
            $this->assertSame($report->job->customer->user->id, $report->reporter_id);
            $this->assertSame($report->job->craftman->user->id, $report->reported_id);
            $this->assertNotSame($report->reporter_id, $report->reported_id);
        }
    }

    public function test_required_database_seeder_is_repeat_safe_and_does_not_seed_demo_marketplace_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(6, Profession::count());
        $this->assertSame(5, City::count());
        $this->assertSame(48, Area::count());
        $this->assertSame(1, User::cmsAdmin()->count());
        $this->assertSame(0, Customer::count());
        $this->assertSame(0, Order::count());
    }

    public function test_demo_seeder_populates_a_complete_consistent_marketplace(): void
    {
        $this->seed(HerafyDemoSeeder::class);

        $this->assertSame(2, User::cmsAdmin()->count());
        $this->assertSame(20, Customer::count());
        $this->assertSame(24, Craftman::count());
        $this->assertSame(50, Order::count());
        $this->assertSame(70, Offer::count());
        $this->assertSame(20, ServiceJob::count());
        $this->assertSame(10, Review::count());
        $this->assertSame(24, Verification::count());
        $this->assertSame(12, Report::count());

        $this->assertFalse(Order::all()->contains(fn (Order $order) => $order->area->city_id !== $order->city_id));
        $this->assertFalse(Offer::all()->contains(fn (Offer $offer) => $offer->order->profession_id !== $offer->craftman->profession_id));
        $this->assertFalse(User::whereNotNull('actor_type')->get()->contains(fn (User $user) => ! $user->isCustomer() && ! $user->isCraftman()));
    }

    private function assertJobConsistency(ServiceJob $job, string $linkedStatus): void
    {
        $job->refresh();
        $this->assertSame($job->offer->order_id, $job->order_id);
        $this->assertSame($job->order->customer_id, $job->customer_id);
        $this->assertSame($job->offer->craftman_id, $job->craftman_id);
        $this->assertSame($job->order->profession_id, $job->craftman->profession_id);
        $this->assertSame($linkedStatus, $job->order->status);
        $this->assertSame($linkedStatus, $job->offer->status);
    }

    private function assertBetween(int $minimum, int $maximum, int $actual): void
    {
        $this->assertGreaterThanOrEqual($minimum, $actual);
        $this->assertLessThanOrEqual($maximum, $actual);
    }
}
