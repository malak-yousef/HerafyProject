<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_sees_only_their_own_orders_newest_first(): void
    {
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();
        $older = Order::factory()->create([
            'customer_id' => $customer->id,
            'title' => 'My older order',
            'created_at' => now()->subDay(),
        ]);
        $newer = Order::factory()->create([
            'customer_id' => $customer->id,
            'title' => 'My newer order',
            'created_at' => now(),
        ]);
        $other = Order::factory()->create([
            'customer_id' => $otherCustomer->id,
            'title' => 'Another customer order',
        ]);

        $response = $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.customer.orders.index'))
            ->assertOk()
            ->assertSee($older->title)
            ->assertSee($newer->title)
            ->assertDontSee($other->title);

        $response->assertSeeInOrder([$newer->title, $older->title]);
    }

    public function test_customer_can_view_their_order_and_its_offers(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $offer = Offer::factory()->create([
            'order_id' => $order->id,
            'message' => 'Customer-visible proposal',
        ]);

        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.customer.orders.show', $order))
            ->assertOk()
            ->assertSee($order->title)
            ->assertSee($offer->message);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $customer = Customer::factory()->create();
        $otherOrder = Order::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.customer.orders.show', $otherOrder))
            ->assertNotFound();
    }

    public function test_customer_can_cancel_their_pending_order_without_deleting_or_changing_other_fields(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
        $unchangedFields = [
            'customer_id', 'profession_id', 'city_id', 'area_id', 'title', 'description',
            'budget', 'service_date', 'address', 'phone', 'created_at',
        ];
        $unchanged = collect($order->getRawOriginal())->only($unchangedFields)->all();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.cancel', $order), ['status' => 'completed'])
            ->assertRedirect(route('herafy.customer.orders.show', $order));

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertNull($order->deleted_at);
        $this->assertEquals(
            $unchanged,
            collect($order->getRawOriginal())->only($unchangedFields)->all()
        );
    }

    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $customer = Customer::factory()->create();
        $otherOrder = Order::factory()->create(['status' => 'pending']);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.cancel', $otherOrder))
            ->assertNotFound();

        $this->assertSame('pending', $otherOrder->fresh()->status);
    }

    public function test_non_pending_orders_cannot_be_cancelled(): void
    {
        foreach (['accepted', 'in_progress', 'completed', 'cancelled'] as $status) {
            $customer = Customer::factory()->create();
            $order = Order::factory()->create([
                'customer_id' => $customer->id,
                'status' => $status,
            ]);

            $this->actingAs($customer->user, 'customer')
                ->patch(route('herafy.customer.orders.cancel', $order))
                ->assertConflict();

            $this->assertSame($status, $order->fresh()->status);
            auth('customer')->logout();
        }
    }

    public function test_guest_and_craftman_cannot_access_customer_order_routes(): void
    {
        $order = Order::factory()->create();

        $this->get(route('herafy.customer.orders.index'))->assertRedirect(route('login'));
        $this->get(route('herafy.customer.orders.show', $order))->assertRedirect(route('login'));
        $this->patch(route('herafy.customer.orders.cancel', $order))->assertRedirect(route('login'));

        $craftman = Craftman::factory()->create();
        $this->actingAs($craftman->user, 'craftman');

        $this->get(route('herafy.customer.orders.index'))->assertRedirect(route('login'));
        $this->get(route('herafy.customer.orders.show', $order))->assertRedirect(route('login'));
        $this->patch(route('herafy.customer.orders.cancel', $order))->assertRedirect(route('login'));
    }

    public function test_cancelled_order_disappears_from_craftman_marketplace_and_cannot_be_opened(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
        $craftman = Craftman::factory()->create(['profession_id' => $order->profession_id]);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.cancel', $order))
            ->assertRedirect();
        auth('customer')->logout();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.order.index'))
            ->assertOk()
            ->assertDontSee($order->title);

        $this->get(route('herafy.order.show', $order))->assertNotFound();
        $this->get(route('herafy.offer.create', $order))->assertNotFound();
        $this->post(route('herafy.offer.store', $order), [
            'price' => 100,
            'message' => 'This must not be stored.',
        ])->assertNotFound();

        $this->assertDatabaseMissing('offers', [
            'order_id' => $order->id,
            'craftman_id' => $craftman->id,
        ]);
    }
}
