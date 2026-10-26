<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Notifications\NewOfferSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CustomerNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_offer_notifies_only_owning_customer_user_with_safe_payload(): void
    {
        Notification::fake();
        [$customer, $craftman, $order] = $this->offerScenario();
        $otherCustomer = Customer::factory()->create();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')->post(route('herafy.offer.store', $order), [
            'price' => 175.50,
            'message' => 'عرض مناسب',
        ])->assertRedirect(route('herafy.order.show', $order));

        $offer = Offer::whereBelongsTo($order)->sole();
        Notification::assertSentTo($customer->user, NewOfferSubmitted::class);
        $sent = Notification::sent($customer->user, NewOfferSubmitted::class)->sole();
        $data = $sent->toDatabase($customer->user);
        $this->assertSame('new_offer_submitted', $data['event']);
        $this->assertSame($order->id, $data['order_id']);
        $this->assertSame($offer->id, $data['offer_id']);
        $this->assertSame($craftman->user->name, $data['craftman_name']);
        $this->assertEquals('175.50', $data['offered_price']);
        $this->assertSame('herafy.customer.orders.show', $data['destination']);
        $this->assertArrayNotHasKey('message_input', $data);
        Notification::assertNotSentTo($otherCustomer->user, NewOfferSubmitted::class);
        Notification::assertNotSentTo($craftman->user, NewOfferSubmitted::class);
        Notification::assertNotSentTo($otherCraftman->user, NewOfferSubmitted::class);
    }

    public function test_database_notification_is_initially_unread_and_targets_user(): void
    {
        [$customer, $craftman, $order] = $this->offerScenario();

        $this->actingAs($craftman->user, 'craftman')->post(route('herafy.offer.store', $order), [
            'price' => 200,
            'message' => 'عرض',
        ]);

        $notification = $customer->user->notifications()->sole();
        $this->assertNull($notification->read_at);
        $this->assertSame($customer->user->id, $notification->notifiable_id);
        $this->assertSame($customer->user::class, $notification->notifiable_type);
    }

    public function test_invalid_ineligible_and_duplicate_offer_attempts_do_not_notify(): void
    {
        Notification::fake();
        [$customer, $craftman, $order] = $this->offerScenario();

        $this->actingAs($craftman->user, 'craftman')->post(route('herafy.offer.store', $order), [
            'price' => 0,
            'message' => '',
        ])->assertSessionHasErrors(['price', 'message']);
        Notification::assertNothingSent();

        $order->update(['status' => 'accepted']);
        $this->post(route('herafy.offer.store', $order), ['price' => 100, 'message' => 'غير متاح'])->assertNotFound();
        Notification::assertNothingSent();

        $order->update(['status' => 'pending']);
        Offer::factory()->create(['order_id' => $order->id, 'craftman_id' => $craftman->id]);
        $this->post(route('herafy.offer.store', $order), ['price' => 100, 'message' => 'مكرر'])->assertRedirect();
        Notification::assertNothingSent();
        $this->assertSame(1, Offer::where('order_id', $order->id)->where('craftman_id', $craftman->id)->count());
        $this->assertSame(0, $customer->user->notifications()->count());
    }

    public function test_customer_navbar_shows_unread_count_recent_notification_and_escaped_payload(): void
    {
        [$customer, $craftman, $order] = $this->offerScenario();
        $craftman->user->update(['name' => '<script>alert(1)</script>']);
        $this->storeNotification($customer, $craftman, $order);

        $this->actingAs($customer->user, 'customer')->get(route('main'))
            ->assertOk()
            ->assertSee('data-testid="notification-unread-count">1<', false)
            ->assertSee('عرض جديد على طلبك')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('عرض كل الإشعارات');
    }

    public function test_notification_list_is_scoped_and_newest_first(): void
    {
        [$customer, $craftman, $firstOrder] = $this->offerScenario();
        $otherCustomer = Customer::factory()->create();
        $firstOrder->update(['title' => 'الطلب الأقدم']);
        $this->storeNotification($customer, $craftman, $firstOrder);
        $this->travel(1)->second();
        $newOrder = Order::factory()->create(['customer_id' => $customer->id, 'profession_id' => $craftman->profession_id, 'title' => 'الطلب الأحدث']);
        $this->storeNotification($customer, $craftman, $newOrder);
        $otherOrder = Order::factory()->create(['customer_id' => $otherCustomer->id, 'profession_id' => $craftman->profession_id, 'title' => 'طلب خاص بعميل آخر']);
        $this->storeNotification($otherCustomer, $craftman, $otherOrder);

        $this->actingAs($customer->user, 'customer')->get(route('herafy.customer.notifications.index'))
            ->assertOk()
            ->assertSeeInOrder(['الطلب الأحدث', 'الطلب الأقدم'])
            ->assertDontSee('طلب خاص بعميل آخر');
    }

    public function test_customer_can_mark_own_notification_read_and_is_redirected_to_owned_order(): void
    {
        [$customer, $craftman, $order] = $this->offerScenario();
        $notification = $this->storeNotification($customer, $craftman, $order);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.customer.orders.show', $order));

        $this->assertNotNull($notification->fresh()->read_at);
        $this->get(route('main'))->assertDontSee('data-testid="notification-unread-count"', false);
    }

    public function test_customer_cannot_mark_another_users_notification_read(): void
    {
        [$owner, $craftman, $order] = $this->offerScenario();
        $attacker = Customer::factory()->create();
        $notification = $this->storeNotification($owner, $craftman, $order);

        $this->actingAs($attacker->user, 'customer')
            ->patch(route('herafy.customer.notifications.read', $notification->id))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_mark_all_reads_only_authenticated_customers_notifications(): void
    {
        [$customer, $craftman, $order] = $this->offerScenario();
        $otherCustomer = Customer::factory()->create();
        $own = $this->storeNotification($customer, $craftman, $order);
        $otherOrder = Order::factory()->create(['customer_id' => $otherCustomer->id, 'profession_id' => $craftman->profession_id]);
        $other = $this->storeNotification($otherCustomer, $craftman, $otherOrder);

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.notifications.read-all'))
            ->assertRedirect();

        $this->assertNotNull($own->fresh()->read_at);
        $this->assertNull($other->fresh()->read_at);
    }

    public function test_guest_and_craftman_cannot_access_customer_notification_routes(): void
    {
        [$customer, $craftman, $order] = $this->offerScenario();
        $notification = $this->storeNotification($customer, $craftman, $order);

        $this->get(route('herafy.customer.notifications.index'))->assertRedirect(route('login'));
        $this->patch(route('herafy.customer.notifications.read', $notification->id))->assertRedirect(route('login'));
        $this->patch(route('herafy.customer.notifications.read-all'))->assertRedirect(route('login'));

        $this->actingAs($craftman->user, 'craftman')->get(route('herafy.customer.notifications.index'))->assertRedirect(route('login'));
        $this->patch(route('herafy.customer.notifications.read', $notification->id))->assertRedirect(route('login'));
        $this->patch(route('herafy.customer.notifications.read-all'))->assertRedirect(route('login'));
    }

    private function offerScenario(): array
    {
        $profession = Profession::factory()->create(['status' => 'active']);
        $customer = Customer::factory()->create();
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'profession_id' => $profession->id,
            'status' => 'pending',
        ]);

        return [$customer, $craftman, $order];
    }

    private function storeNotification(Customer $customer, Craftman $craftman, Order $order)
    {
        $offer = Offer::factory()->create(['order_id' => $order->id, 'craftman_id' => $craftman->id]);
        $offer->load(['order.customer.user', 'craftman.user']);
        $customer->user->notify(new NewOfferSubmitted($offer));

        return $customer->user->notifications()->latest()->firstOrFail();
    }
}
