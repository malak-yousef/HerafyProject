<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class ServiceJobLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_craftman_starts_pending_job_and_synchronizes_states(): void
    {
        [$job, $craftman, $order, $offer] = $this->pendingJob();
        Carbon::setTestNow('2026-08-22 10:30:00');

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertRedirect(route('herafy.craftman.jobs.show', $job));

        $job->refresh();
        $this->assertSame('in_progress', $job->status);
        $this->assertTrue($job->started_at->equalTo(now()));
        $this->assertNull($job->completed_at);
        $this->assertSame('in_progress', $order->fresh()->status);
        $this->assertSame('in_progress', $offer->fresh()->status);
    }

    public function test_another_craftman_customer_admin_and_guest_cannot_start_job(): void
    {
        [$job] = $this->pendingJob();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($otherCraftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertNotFound();
        auth('craftman')->logout();

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertRedirect(route('login'));
        auth('customer')->logout();

        $admin = User::factory()->create(['actor_type' => null, 'actor_id' => null]);
        $this->actingAs($admin, 'admin')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertRedirect(route('login'));
        auth('admin')->logout();

        $this->patch(route('herafy.craftman.jobs.start', $job))->assertRedirect(route('login'));
        $this->assertSame('pending', $job->fresh()->status);
    }

    public function test_job_cannot_be_started_twice(): void
    {
        [$job, $craftman] = $this->pendingJob();

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertRedirect();
        $startedAt = $job->fresh()->started_at;

        $this->patch(route('herafy.craftman.jobs.start', $job))->assertConflict();
        $this->assertTrue($job->fresh()->started_at->equalTo($startedAt));
    }

    public function test_assigned_craftman_completes_in_progress_job_and_preserves_start_time(): void
    {
        [$job, $craftman, $order, $offer] = $this->inProgressJob();
        $startedAt = $job->started_at;
        Carbon::setTestNow('2026-08-22 15:45:00');

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.complete', $job))
            ->assertRedirect(route('herafy.craftman.jobs.show', $job));

        $job->refresh();
        $this->assertSame('completed', $job->status);
        $this->assertTrue($job->started_at->equalTo($startedAt));
        $this->assertTrue($job->completed_at->equalTo(now()));
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame('completed', $offer->fresh()->status);
    }

    public function test_pending_job_cannot_jump_to_completed(): void
    {
        [$job, $craftman, $order, $offer] = $this->pendingJob();

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.complete', $job))
            ->assertConflict();

        $this->assertSame('pending', $job->fresh()->status);
        $this->assertSame('accepted', $order->fresh()->status);
        $this->assertSame('accepted', $offer->fresh()->status);
    }

    public function test_another_craftman_cannot_complete_job(): void
    {
        [$job] = $this->inProgressJob();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($otherCraftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.complete', $job))
            ->assertNotFound();

        $this->assertSame('in_progress', $job->fresh()->status);
    }

    public function test_job_cannot_be_completed_twice(): void
    {
        [$job, $craftman] = $this->inProgressJob();

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.complete', $job))
            ->assertRedirect();
        $completedAt = $job->fresh()->completed_at;

        $this->patch(route('herafy.craftman.jobs.complete', $job))->assertConflict();
        $this->assertTrue($job->fresh()->completed_at->equalTo($completedAt));
    }

    public function test_cancelled_job_cannot_start_or_complete(): void
    {
        [$job, $craftman, $order, $offer] = $this->pendingJob();
        $job->update(['status' => 'cancelled']);

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertConflict();
        $this->patch(route('herafy.craftman.jobs.complete', $job))->assertConflict();

        $this->assertSame('cancelled', $job->fresh()->status);
        $this->assertSame('accepted', $order->fresh()->status);
        $this->assertSame('accepted', $offer->fresh()->status);
    }

    public function test_inconsistent_linked_state_fails_without_partial_update(): void
    {
        [$job, $craftman, $order, $offer] = $this->pendingJob();
        $offer->update(['status' => 'completed']);

        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.jobs.start', $job))
            ->assertConflict();

        $this->assertSame('pending', $job->fresh()->status);
        $this->assertSame('accepted', $order->fresh()->status);
        $this->assertSame('completed', $offer->fresh()->status);
    }

    public function test_transition_failure_rolls_back_all_linked_updates(): void
    {
        [$job, $craftman, $order, $offer] = $this->pendingJob();

        DB::listen(function (QueryExecuted $query): void {
            if (str_contains(strtolower($query->sql), 'update "service_jobs"')) {
                throw new RuntimeException('Simulated lifecycle persistence failure.');
            }
        });
        $this->withoutExceptionHandling();

        try {
            $this->actingAs($craftman->user, 'craftman')
                ->patch(route('herafy.craftman.jobs.start', $job));
            $this->fail('Expected lifecycle persistence failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated lifecycle persistence failure.', $exception->getMessage());
        }

        $this->assertSame('pending', $job->fresh()->status);
        $this->assertNull($job->fresh()->started_at);
        $this->assertSame('accepted', $order->fresh()->status);
        $this->assertSame('accepted', $offer->fresh()->status);
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

        return [$job, $offer->craftman, $order, $offer];
    }

    private function inProgressJob(): array
    {
        [$job, $craftman, $order, $offer] = $this->pendingJob();
        $startedAt = now()->subHour();
        $job->update(['status' => 'in_progress', 'started_at' => $startedAt]);
        $order->update(['status' => 'in_progress']);
        $offer->update(['status' => 'in_progress']);

        return [$job->fresh(), $craftman, $order->fresh(), $offer->fresh()];
    }
}
