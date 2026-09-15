<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ServiceJob;
use App\Notifications\ServiceJobCompleted;
use App\Notifications\ServiceJobStarted;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class ServiceJobLifecycleNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_notifies_only_owning_customer_user_with_safe_payload(): void
    {
        Notification::fake();
        [$job, $customer, $craftman, $order] = $this->pendingJob();
        $otherCustomer = Customer::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertRedirect(route('herafy.craftman.jobs.show', $job));

        Notification::assertSentTo($customer->user, ServiceJobStarted::class);
        Notification::assertNotSentTo($otherCustomer->user, ServiceJobStarted::class);
        Notification::assertNotSentTo($craftman->user, ServiceJobStarted::class);
        $sent = Notification::sent($customer->user, ServiceJobStarted::class)->sole();
        $data = $sent->toDatabase($customer->user);
        $this->assertSame('service_job_started', $data['event']);
        $this->assertSame($job->id, $data['service_job_id']);
        $this->assertSame($order->id, $data['order_id']);
        $this->assertSame($order->title, $data['order_title']);
        $this->assertSame($craftman->user->name, $data['craftman_name']);
        $this->assertNotEmpty($data['started_at']);
        $this->assertSame('herafy.customer.orders.show', $data['destination']);
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('address', $data);
    }

    public function test_completion_notifies_only_owning_customer_user_with_safe_payload(): void
    {
        Notification::fake();
        [$job, $customer, $craftman, $order] = $this->inProgressJob();
        $otherCustomer = Customer::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.complete', $job))
            ->assertRedirect(route('herafy.craftman.jobs.show', $job));

        Notification::assertSentTo($customer->user, ServiceJobCompleted::class);
        Notification::assertNotSentTo($otherCustomer->user, ServiceJobCompleted::class);
        Notification::assertNotSentTo($craftman->user, ServiceJobCompleted::class);
        $sent = Notification::sent($customer->user, ServiceJobCompleted::class)->sole();
        $data = $sent->toDatabase($customer->user);
        $this->assertSame('service_job_completed', $data['event']);
        $this->assertSame($job->id, $data['service_job_id']);
        $this->assertSame($order->id, $data['order_id']);
        $this->assertSame($order->title, $data['order_title']);
        $this->assertSame($craftman->user->name, $data['craftman_name']);
        $this->assertNotEmpty($data['completed_at']);
        $this->assertSame('herafy.customer.orders.show', $data['destination']);
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('address', $data);
    }

    public function test_lifecycle_database_notifications_begin_unread_and_appear_in_customer_ui(): void
    {
        [$job, $customer, $craftman, $order] = $this->pendingJob();
        $this->actingAs($craftman->user, 'craftman')->patch(route('herafy.craftman.jobs.start', $job));
        $this->patch(route('herafy.craftman.jobs.complete', $job));
        auth('craftman')->logout();

        $this->assertSame(2, $customer->user->unreadNotifications()->count());
        $this->actingAs($customer->user, 'customer')->get(route('main'))
            ->assertOk()
            ->assertSee('data-testid="notification-unread-count">2<', false)
            ->assertSee('بدأ العمل على طلبك')
            ->assertSee('تم إكمال العمل');
        $this->get(route('herafy.customer.notifications.index'))
            ->assertOk()
            ->assertSeeInOrder(['تم إكمال العمل', 'بدأ العمل على طلبك'])
            ->assertSee($order->title);
    }

    public function test_opening_lifecycle_notification_marks_it_read_and_redirects_only_to_owned_order(): void
    {
        [$job, $customer, $craftman, $order] = $this->pendingJob();
        $this->actingAs($craftman->user, 'craftman')->patch(route('herafy.craftman.jobs.start', $job));
        auth('craftman')->logout();
        $notification = $customer->user->notifications()->sole();
        $otherCustomer = Customer::factory()->create();

        $this->actingAs($otherCustomer->user, 'customer')
            ->patch(route('herafy.customer.notifications.read', $notification->id))
            ->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.customer.orders.show', $order));
        $this->assertNotNull($notification->fresh()->read_at);
        $this->get(route('main'))->assertDontSee('data-testid="notification-unread-count"', false);
    }

    public function test_repeated_and_invalid_start_create_no_extra_notification(): void
    {
        [$job, $customer, $craftman] = $this->pendingJob();
        $this->actingAs($craftman->user, 'craftman')->patch(route('herafy.craftman.jobs.start', $job))->assertRedirect();
        $this->patch(route('herafy.craftman.jobs.start', $job))->assertConflict();

        $this->assertSame(1, $customer->user->notifications()->where('type', ServiceJobStarted::class)->count());

        [$invalidJob, $invalidCustomer, $invalidCraftman] = $this->pendingJob();
        $invalidJob->update(['status' => 'cancelled']);
        $this->actingAs($invalidCraftman->user, 'craftman')->patch(route('herafy.craftman.jobs.start', $invalidJob))->assertConflict();
        $this->assertSame(0, $invalidCustomer->user->notifications()->count());
    }

    public function test_repeated_and_invalid_completion_create_no_extra_notification(): void
    {
        [$job, $customer, $craftman] = $this->inProgressJob();
        $this->actingAs($craftman->user, 'craftman')->patch(route('herafy.craftman.jobs.complete', $job))->assertRedirect();
        $this->patch(route('herafy.craftman.jobs.complete', $job))->assertConflict();

        $this->assertSame(1, $customer->user->notifications()->where('type', ServiceJobCompleted::class)->count());

        [$invalidJob, $invalidCustomer, $invalidCraftman] = $this->pendingJob();
        $this->actingAs($invalidCraftman->user, 'craftman')->patch(route('herafy.craftman.jobs.complete', $invalidJob))->assertConflict();
        $this->assertSame(0, $invalidCustomer->user->notifications()->count());
    }

    public function test_failed_start_transaction_creates_no_notification(): void
    {
        Notification::fake();
        [$job, $customer, $craftman, $order, $offer] = $this->pendingJob();
        $this->failLifecycleUpdate();

        try {
            $this->actingAs($craftman->user, 'craftman')->patch(route('herafy.craftman.jobs.start', $job));
            $this->fail('Expected lifecycle persistence failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated lifecycle persistence failure.', $exception->getMessage());
        }

        Notification::assertNothingSent();
        $this->assertSame('pending', $job->fresh()->status);
        $this->assertSame('accepted', $order->fresh()->status);
        $this->assertSame('accepted', $offer->fresh()->status);
        $this->assertSame(0, $customer->user->notifications()->count());
    }

    public function test_failed_completion_transaction_creates_no_notification(): void
    {
        Notification::fake();
        [$job, $customer, $craftman, $order, $offer] = $this->inProgressJob();
        $this->failLifecycleUpdate();

        try {
            $this->actingAs($craftman->user, 'craftman')->patch(route('herafy.craftman.jobs.complete', $job));
            $this->fail('Expected lifecycle persistence failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated lifecycle persistence failure.', $exception->getMessage());
        }

        Notification::assertNothingSent();
        $this->assertSame('in_progress', $job->fresh()->status);
        $this->assertSame('in_progress', $order->fresh()->status);
        $this->assertSame('in_progress', $offer->fresh()->status);
        $this->assertSame(0, $customer->user->notifications()->count());
    }

    private function pendingJob(): array
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id, 'status' => 'accepted']);
        $offer = Offer::factory()->create(['order_id' => $order->id, 'status' => 'accepted']);
        $job = ServiceJob::factory()->create([
            'order_id' => $order->id,
            'offer_id' => $offer->id,
            'customer_id' => $customer->id,
            'craftman_id' => $offer->craftman_id,
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
        ]);

        return [$job, $customer, $offer->craftman, $order, $offer];
    }

    private function inProgressJob(): array
    {
        [$job, $customer, $craftman, $order, $offer] = $this->pendingJob();
        $job->update(['status' => 'in_progress', 'started_at' => now()->subHour()]);
        $order->update(['status' => 'in_progress']);
        $offer->update(['status' => 'in_progress']);

        return [$job->fresh(), $customer, $craftman, $order->fresh(), $offer->fresh()];
    }

    private function failLifecycleUpdate(): void
    {
        DB::listen(function (QueryExecuted $query): void {
            if (str_contains(strtolower($query->sql), 'update "service_jobs"')) {
                throw new RuntimeException('Simulated lifecycle persistence failure.');
            }
        });
        $this->withoutExceptionHandling();
    }
}
