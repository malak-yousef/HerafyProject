<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOfferAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_accepts_one_offer_on_their_pending_order(): void
    {
        [$customer, $order, $selected, $other] = $this->acceptanceScenario();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $selected]))
            ->assertRedirect(route('herafy.customer.orders.show', $order));

        $this->assertSame('accepted', $selected->fresh()->status);
        $this->assertSame('accepted', $order->fresh()->status);

        // The enum has no rejected/declined state, so non-selected offers remain pending but closed by the Order state.
        $this->assertSame('pending', $other->fresh()->status);
        $this->assertSame(1, Offer::where('order_id', $order->id)->where('status', 'accepted')->count());
    }

    public function test_customer_cannot_accept_an_offer_on_another_customers_order(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create();
        $offer = Offer::factory()->create(['order_id' => $order->id]);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]))
            ->assertNotFound();

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('pending', $offer->fresh()->status);
    }

    public function test_customer_cannot_accept_offer_from_a_different_order(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $otherOrder = Order::factory()->create(['customer_id' => $customer->id]);
        $foreignOffer = Offer::factory()->create(['order_id' => $otherOrder->id]);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $foreignOffer]))
            ->assertNotFound();

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('pending', $foreignOffer->fresh()->status);
    }

    public function test_guest_and_craftman_cannot_use_customer_acceptance_route(): void
    {
        $order = Order::factory()->create();
        $offer = Offer::factory()->create(['order_id' => $order->id]);

        $this->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]))
            ->assertRedirect(route('login'));

        $craftman = Craftman::factory()->create();
        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]))
            ->assertRedirect(route('login'));

        $this->assertSame('pending', $offer->fresh()->status);
    }

    public function test_second_or_repeated_acceptance_fails_without_corrupting_state(): void
    {
        [$customer, $order, $selected, $other] = $this->acceptanceScenario();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $selected]))
            ->assertRedirect();

        $this->patch(route('herafy.customer.orders.offers.accept', [$order, $other]))
            ->assertConflict();
        $this->patch(route('herafy.customer.orders.offers.accept', [$order, $selected]))
            ->assertConflict();

        $this->assertSame('accepted', $selected->fresh()->status);
        $this->assertSame('pending', $other->fresh()->status);
        $this->assertSame('accepted', $order->fresh()->status);
        $this->assertSame(1, Offer::where('order_id', $order->id)->where('status', 'accepted')->count());
    }

    public function test_non_pending_orders_cannot_accept_offers(): void
    {
        foreach (['cancelled', 'in_progress', 'completed', 'accepted'] as $status) {
            $customer = Customer::factory()->create();
            $order = Order::factory()->create([
                'customer_id' => $customer->id,
                'status' => $status,
            ]);
            $offer = Offer::factory()->create(['order_id' => $order->id, 'status' => 'pending']);

            $this->actingAs($customer->user, 'customer')
                ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]))
                ->assertConflict();

            $this->assertSame($status, $order->fresh()->status);
            $this->assertSame('pending', $offer->fresh()->status);
            auth('customer')->logout();
        }
    }

    public function test_non_pending_or_invalid_craftman_offer_cannot_be_accepted(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $nonPending = Offer::factory()->create([
            'order_id' => $order->id,
            'status' => 'cancelled',
        ]);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $nonPending]))
            ->assertConflict();

        $craftman = Craftman::factory()->create();
        $missingCraftmanOffer = Offer::factory()->create([
            'order_id' => $order->id,
            'craftman_id' => $craftman->id,
        ]);
        $craftman->delete();

        $this->patch(route('herafy.customer.orders.offers.accept', [$order, $missingCraftmanOffer]))
            ->assertConflict();
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_acceptance_closes_craftman_discovery_and_offer_submission(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();
        $craftman = $selected->craftman;

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $selected]))
            ->assertRedirect();
        auth('customer')->logout();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.order.index'))
            ->assertOk()
            ->assertDontSee($order->title);
        $this->get(route('herafy.order.show', $order))->assertNotFound();
        $this->post(route('herafy.offer.store', $order), [
            'price' => 500,
            'message' => 'Late offer',
        ])->assertNotFound();

        $this->assertSame(2, Offer::where('order_id', $order->id)->count());
    }

    public function test_accepted_craftman_still_sees_their_offer(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $selected]));
        auth('customer')->logout();

        $this->actingAs($selected->craftman->user, 'craftman')
            ->get(route('herafy.offer.index'))
            ->assertOk()
            ->assertSee($selected->message);
    }

    private function acceptanceScenario(): array
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
        $selected = Offer::factory()->create([
            'order_id' => $order->id,
            'status' => 'pending',
            'message' => 'Selected proposal',
        ]);
        $other = Offer::factory()->create([
            'order_id' => $order->id,
            'status' => 'pending',
            'message' => 'Other proposal',
        ]);

        return [$customer, $order, $selected, $other];
    }
}
