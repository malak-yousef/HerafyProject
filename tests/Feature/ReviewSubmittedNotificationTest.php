<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Review;
use App\Models\ServiceJob;
use App\Notifications\ReviewSubmitted;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class ReviewSubmittedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_review_notifies_only_reviewed_craftman_user_with_safe_published_payload(): void
    {
        Notification::fake();
        [$job, $customer, $craftman, $order] = $this->completedJob();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($customer->user, 'customer')->post(
            route('herafy.customer.jobs.review.store', $job),
            ['rating' => 5, 'comment' => 'عمل ممتاز وسريع'],
        )->assertRedirect(route('herafy.customer.orders.show', $order));

        $review = Review::sole();
        Notification::assertSentTo($craftman->user, ReviewSubmitted::class);
        Notification::assertNotSentTo($otherCraftman->user, ReviewSubmitted::class);
        Notification::assertNotSentTo($customer->user, ReviewSubmitted::class);
        $sent = Notification::sent($craftman->user, ReviewSubmitted::class)->sole();
        $data = $sent->toDatabase($craftman->user);
        $this->assertSame('review_submitted', $data['event']);
        $this->assertSame('وصلك تقييم جديد', $data['title']);
        $this->assertStringContainsString('تم نشر', $data['message']);
        $this->assertSame($review->id, $data['review_id']);
        $this->assertSame($order->id, $data['order_id']);
        $this->assertSame($job->id, $data['service_job_id']);
        $this->assertSame($order->title, $data['order_title']);
        $this->assertSame(5, $data['rating']);
        $this->assertSame('عمل ممتاز وسريع', $data['comment_preview']);
        $this->assertSame('approved', $data['moderation_status']);
        $this->assertSame('herafy.craftman.jobs.show', $data['destination']);
        $this->assertArrayNotHasKey('email', $data);
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('address', $data);
    }

    public function test_database_review_notification_begins_unread_and_uses_user_recipient(): void
    {
        [$job, $customer, $craftman] = $this->completedJob();
        $this->submitReview($customer, $job);

        $notification = $craftman->user->notifications()->sole();
        $this->assertNull($notification->read_at);
        $this->assertSame($craftman->user->id, $notification->notifiable_id);
        $this->assertSame($craftman->user::class, $notification->notifiable_type);
        $this->assertSame('approved', Review::sole()->status);
    }

    public function test_premature_and_unauthorized_review_attempts_create_no_notification(): void
    {
        Notification::fake();

        foreach (['pending', 'in_progress'] as $status) {
            [$job, $customer] = $this->completedJob();
            $job->update(['status' => $status, 'completed_at' => null]);
            $this->actingAs($customer->user, 'customer')
                ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 4])
                ->assertConflict();
            auth('customer')->logout();
        }

        [$job] = $this->completedJob();
        $otherCustomer = Customer::factory()->create();
        $this->actingAs($otherCustomer->user, 'customer')
            ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 5])
            ->assertNotFound();

        Notification::assertNothingSent();
        $this->assertSame(0, Review::count());
    }

    public function test_duplicate_review_attempt_creates_no_duplicate_notification(): void
    {
        [$job, $customer, $craftman] = $this->completedJob();
        $this->submitReview($customer, $job)->assertRedirect();
        $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.jobs.review.store', $job), ['rating' => 2])
            ->assertConflict();

        $this->assertSame(1, Review::count());
        $this->assertSame(1, $craftman->user->notifications()->where('type', ReviewSubmitted::class)->count());
    }

    public function test_failed_review_transaction_creates_no_notification(): void
    {
        Notification::fake();
        [$job, $customer] = $this->completedJob();
        DB::listen(function (QueryExecuted $query): void {
            if (str_contains(strtolower($query->sql), 'insert into "reviews"')) {
                throw new RuntimeException('Simulated Review failure.');
            }
        });
        $this->withoutExceptionHandling();

        try {
            $this->submitReview($customer, $job);
            $this->fail('Expected Review persistence failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated Review failure.', $exception->getMessage());
        }

        Notification::assertNothingSent();
        $this->assertSame(0, Review::count());
    }

    public function test_craftman_ui_displays_review_notification_and_read_redirect_is_owned_job(): void
    {
        [$job, $customer, $craftman, $order] = $this->completedJob();
        $this->submitReview($customer, $job, ['rating' => 4, 'comment' => 'تعليق آمن']);
        auth('customer')->logout();
        $notification = $craftman->user->notifications()->sole();

        $this->actingAs($craftman->user, 'craftman')->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee('data-testid="notification-unread-count">1<', false)
            ->assertSee('وصلك تقييم جديد')
            ->assertSee('تم نشر تقييم جديد');
        $this->get(route('herafy.craftman.notifications.index'))
            ->assertOk()
            ->assertSee($order->title)
            ->assertSee('تعليق آمن');
        $this->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.craftman.jobs.show', $job));
        $this->assertNotNull($notification->fresh()->read_at);
        $this->get(route('herafy.craftman.dashboard'))->assertDontSee('data-testid="notification-unread-count"', false);
    }

    public function test_another_craftman_cannot_read_review_notification_and_mark_all_is_scoped(): void
    {
        [$job, $customer, $craftman] = $this->completedJob();
        $this->submitReview($customer, $job);
        auth('customer')->logout();
        $notification = $craftman->user->notifications()->sole();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($otherCraftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertNotFound();
        $this->patch(route('herafy.craftman.notifications.read-all'))->assertRedirect();
        $this->assertNull($notification->fresh()->read_at);

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read-all'))
            ->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
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

    private function submitReview(Customer $customer, ServiceJob $job, array $overrides = [])
    {
        return $this->actingAs($customer->user, 'customer')->post(
            route('herafy.customer.jobs.review.store', $job),
            array_merge(['rating' => 5, 'comment' => 'عمل ممتاز'], $overrides),
        );
    }
}
