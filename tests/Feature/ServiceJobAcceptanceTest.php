<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ServiceJob;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class ServiceJobAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_offer_creates_correct_pending_service_job(): void
    {
        [$customer, $order, $offer] = $this->acceptanceScenario();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]))
            ->assertRedirect(route('herafy.customer.orders.show', $order));

        $job = ServiceJob::sole();
        $this->assertSame($order->id, $job->order_id);
        $this->assertSame($offer->id, $job->offer_id);
        $this->assertSame($customer->id, $job->customer_id);
        $this->assertSame($offer->craftman_id, $job->craftman_id);
        $this->assertSame('pending', $job->status);
        $this->assertNull($job->started_at);
        $this->assertNull($job->completed_at);
        $this->assertSame($offer->price, $job->offer->price);
        $this->assertSame('accepted', $offer->fresh()->status);
        $this->assertSame('accepted', $order->fresh()->status);
    }

    public function test_service_job_failure_rolls_back_offer_and_order_acceptance(): void
    {
        [$customer, $order, $offer] = $this->acceptanceScenario();

        DB::listen(function (QueryExecuted $query): void {
            if (str_contains(strtolower($query->sql), 'insert into "service_jobs"')) {
                throw new RuntimeException('Simulated ServiceJob failure.');
            }
        });
        $this->withoutExceptionHandling();

        try {
            $this->actingAs($customer->user, 'customer')
                ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]));
            $this->fail('Expected ServiceJob creation failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated ServiceJob failure.', $exception->getMessage());
        }

        $this->assertSame('pending', $offer->fresh()->status);
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertDatabaseCount('service_jobs', 0);
    }

    public function test_repeated_acceptance_does_not_create_second_service_job(): void
    {
        [$customer, $order, $offer] = $this->acceptanceScenario();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]))
            ->assertRedirect();
        $this->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]))
            ->assertConflict();

        $this->assertDatabaseCount('service_jobs', 1);
        $this->assertSame('accepted', $order->fresh()->status);
        $this->assertSame('accepted', $offer->fresh()->status);
    }

    public function test_database_blocks_duplicate_service_job_for_same_order(): void
    {
        [$customer, $order, $offer] = $this->acceptanceScenario();
        ServiceJob::create($this->jobAttributes($customer, $order, $offer));
        $otherOffer = Offer::factory()->create(['order_id' => $order->id]);

        $this->expectException(QueryException::class);
        ServiceJob::create($this->jobAttributes($customer, $order, $otherOffer));
    }

    public function test_database_blocks_duplicate_service_job_for_same_offer(): void
    {
        [$customer, $order, $offer] = $this->acceptanceScenario();
        ServiceJob::create($this->jobAttributes($customer, $order, $offer));
        $otherOrder = Order::factory()->create(['customer_id' => $customer->id]);

        $this->expectException(QueryException::class);
        ServiceJob::create($this->jobAttributes($customer, $otherOrder, $offer));
    }

    public function test_customer_sees_service_job_only_through_owned_order(): void
    {
        [$customer, $order, $offer] = $this->acceptanceScenario();
        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]));
        $job = ServiceJob::sole();

        $this->get(route('herafy.customer.orders.show', $order))
            ->assertOk()
            ->assertSee('اتفاق العمل')
            ->assertSee((string) $job->offer->price);

        $otherCustomer = Customer::factory()->create();
        $this->actingAs($otherCustomer->user, 'customer')
            ->get(route('herafy.customer.orders.show', $order))
            ->assertNotFound();
    }

    public function test_craftman_sees_only_their_own_service_jobs(): void
    {
        [$customer, $order, $offer] = $this->acceptanceScenario();
        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]));
        auth('customer')->logout();
        $job = ServiceJob::sole();

        $this->actingAs($offer->craftman->user, 'craftman')
            ->get(route('herafy.craftman.jobs.index'))
            ->assertOk()
            ->assertSee($order->title);
        $this->get(route('herafy.craftman.jobs.show', $job))
            ->assertOk()
            ->assertSee($order->description);

        $otherCraftman = Craftman::factory()->create();
        $this->actingAs($otherCraftman->user, 'craftman')
            ->get(route('herafy.craftman.jobs.show', $job))
            ->assertNotFound();
    }

    public function test_guest_cannot_access_craftman_service_job_routes(): void
    {
        $job = ServiceJob::factory()->create();

        $this->get(route('herafy.craftman.jobs.index'))->assertRedirect(route('login'));
        $this->get(route('herafy.craftman.jobs.show', $job))->assertRedirect(route('login'));
    }

    private function acceptanceScenario(): array
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
        $offer = Offer::factory()->create([
            'order_id' => $order->id,
            'status' => 'pending',
            'price' => 725,
        ]);

        return [$customer, $order, $offer];
    }

    private function jobAttributes(Customer $customer, Order $order, Offer $offer): array
    {
        return [
            'order_id' => $order->id,
            'offer_id' => $offer->id,
            'customer_id' => $customer->id,
            'craftman_id' => $offer->craftman_id,
            'status' => 'pending',
        ];
    }
}
