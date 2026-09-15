<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\CustomerReview;
use App\Models\ServiceJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CraftmanCustomerReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_craftman_can_review_customer_once_after_completed_owned_job(): void
    {
        $job = ServiceJob::factory()->completed()->create();

        $this->actingAs($job->craftman->user, 'craftman')
            ->post(route('herafy.craftman.jobs.customer-review.store', $job), [
                'rating' => 5,
                'comment' => 'عميل ملتزم ومتعاون',
            ])
            ->assertRedirect(route('herafy.craftman.jobs.show', $job))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customer_reviews', [
            'service_job_id' => $job->id,
            'craftman_id' => $job->craftman_id,
            'customer_id' => $job->customer_id,
            'rating' => 5,
            'comment' => 'عميل ملتزم ومتعاون',
        ]);

        $this->actingAs($job->craftman->user, 'craftman')
            ->post(route('herafy.craftman.jobs.customer-review.store', $job), ['rating' => 4])
            ->assertConflict();

        $this->assertDatabaseCount('customer_reviews', 1);
    }

    public function test_review_is_rejected_before_completion_and_for_another_craftmans_job(): void
    {
        $pending = ServiceJob::factory()->pending()->create();

        $this->actingAs($pending->craftman->user, 'craftman')
            ->post(route('herafy.craftman.jobs.customer-review.store', $pending), ['rating' => 5])
            ->assertConflict();

        $otherCraftman = Craftman::factory()->active()->create();
        $completed = ServiceJob::factory()->completed()->create();

        $this->actingAs($otherCraftman->user, 'craftman')
            ->post(route('herafy.craftman.jobs.customer-review.store', $completed), ['rating' => 5])
            ->assertNotFound();

        $this->assertDatabaseCount('customer_reviews', 0);
    }

    public function test_both_sides_see_the_customer_review_and_customer_profile_summary(): void
    {
        $job = ServiceJob::factory()->completed()->create();
        CustomerReview::create([
            'service_job_id' => $job->id,
            'craftman_id' => $job->craftman_id,
            'customer_id' => $job->customer_id,
            'rating' => 4,
            'comment' => 'تواصل ممتاز من العميل',
        ]);

        $this->actingAs($job->craftman->user, 'craftman')
            ->get(route('herafy.craftman.jobs.show', $job))
            ->assertOk()
            ->assertSee('تقييمك للعميل')
            ->assertSee('تواصل ممتاز من العميل');

        $this->actingAs($job->customer->user, 'customer')
            ->get(route('herafy.customer.orders.show', $job->order))
            ->assertOk()
            ->assertSee('تقييم الحرفي لك')
            ->assertSee('تواصل ممتاز من العميل');

        $this->actingAs($job->customer->user, 'customer')
            ->get(route('herafy.customer.profile.show'))
            ->assertOk()
            ->assertSee('4.0')
            ->assertSee('(1 تقييم من الحرفيين)')
            ->assertSee('تواصل ممتاز من العميل');
    }
}
