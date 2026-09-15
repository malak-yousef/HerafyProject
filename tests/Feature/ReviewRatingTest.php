<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Review;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_reviews_completed_service_job_with_server_derived_mappings(): void
    {
        [$job, $customer, $craftman, $order] = $this->completedJob();
        $otherCustomer = Customer::factory()->create();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.jobs.review.store', $job), [
                'rating' => 5,
                'comment' => 'Excellent completed work.',
                'customer_id' => $otherCustomer->id,
                'craftsman_id' => $otherCraftman->id,
                'request_id' => Order::factory()->create()->id,
                'status' => 'approved',
            ])
            ->assertRedirect(route('herafy.customer.orders.show', $order));

        $review = Review::sole();
        $this->assertSame(5, $review->rating);
        $this->assertSame('Excellent completed work.', $review->comment);
        $this->assertSame($customer->id, $review->customer_id);
        $this->assertSame($craftman->id, $review->craftsman_id);
        $this->assertSame($order->id, $review->request_id);
        $this->assertSame('approved', $review->status);
        $this->assertSame($review->id, $job->review->id);
    }

    public function test_pending_in_progress_and_cancelled_jobs_cannot_be_reviewed(): void
    {
        foreach (['pending', 'in_progress', 'cancelled'] as $status) {
            [$job, $customer] = $this->completedJob();
            $job->update(['status' => $status, 'completed_at' => null]);

            $this->actingAs($customer->user, 'customer')
                ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 4])
                ->assertConflict();

            auth('customer')->logout();
        }

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_inconsistent_completed_order_or_offer_cannot_be_reviewed(): void
    {
        [$job, $customer, , $order, $offer] = $this->completedJob();
        $order->update(['status' => 'in_progress']);

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 4])
            ->assertConflict();

        $order->update(['status' => 'completed']);
        $offer->update(['status' => 'in_progress']);
        $this->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 4])
            ->assertConflict();

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_another_customer_cannot_review_service_job(): void
    {
        [$job] = $this->completedJob();
        $otherCustomer = Customer::factory()->create();

        $this->actingAs($otherCustomer->user, 'customer')
            ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 5])
            ->assertNotFound();

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_craftman_admin_and_guest_cannot_submit_customer_review(): void
    {
        [$job, , $craftman] = $this->completedJob();

        $this->actingAs($craftman->user, 'craftman')
            ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 5])
            ->assertRedirect(route('login'));
        auth('craftman')->logout();

        $admin = User::factory()->create(['actor_type' => null, 'actor_id' => null]);
        $this->actingAs($admin, 'admin')
            ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 5])
            ->assertRedirect(route('login'));
        auth('admin')->logout();

        $this->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 5])
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_duplicate_review_is_rejected_and_database_constraint_blocks_bypass(): void
    {
        [$job, $customer, $craftman, $order] = $this->completedJob();

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 4])
            ->assertRedirect();
        $this->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 2])
            ->assertConflict();
        $this->assertDatabaseCount('reviews', 1);

        $this->expectException(QueryException::class);
        Review::create([
            'rating' => 3,
            'craftsman_id' => $craftman->id,
            'customer_id' => $customer->id,
            'request_id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_rating_comment_and_html_validation(): void
    {
        [$job, $customer] = $this->completedJob();

        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.jobs.review.store', $job), [
                'rating' => 0,
                'comment' => '<script>alert(1)</script>',
            ])
            ->assertSessionHasErrors(['rating', 'comment']);

        $this->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 6])
            ->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_customer_and_craftman_see_review_and_relationship_aggregates_are_correct(): void
    {
        [$job, $customer, $craftman, $order] = $this->completedJob();
        $review = Review::create([
            'rating' => 5,
            'comment' => 'Visible feedback',
            'craftsman_id' => $craftman->id,
            'customer_id' => $customer->id,
            'request_id' => $order->id,
            'status' => 'pending',
        ]);

        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Visible feedback')
            ->assertDontSee(route('herafy.customer.jobs.review.store', $job));
        auth('customer')->logout();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.craftman.jobs.show', $job))
            ->assertOk()
            ->assertSee('Visible feedback');

        $this->assertSame($review->id, $craftman->reviews()->sole()->id);
        $this->assertSame(1, $craftman->reviews()->count());
        $this->assertEquals(5.0, $craftman->reviews()->avg('rating'));
    }

    private function completedJob(): array
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id, 'status' => 'completed']);
        $offer = Offer::factory()->create(['order_id' => $order->id, 'status' => 'completed']);
        $job = ServiceJob::factory()->create([
            'order_id' => $order->id,
            'offer_id' => $offer->id,
            'customer_id' => $customer->id,
            'craftman_id' => $offer->craftman_id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);

        return [$job, $customer, $offer->craftman, $order, $offer];
    }
}
