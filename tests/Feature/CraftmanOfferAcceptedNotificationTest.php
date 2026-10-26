<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ServiceJob;
use App\Notifications\OfferAccepted;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class CraftmanOfferAcceptedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_acceptance_notifies_only_selected_craftman_user_with_correct_payload(): void
    {
        Notification::fake();
        [$customer, $order, $selected, $other] = $this->acceptanceScenario();

        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $selected]))
            ->assertRedirect(route('herafy.customer.orders.show', $order));

        $job = ServiceJob::sole();
        Notification::assertSentTo($selected->craftman->user, OfferAccepted::class);
        Notification::assertNotSentTo($other->craftman->user, OfferAccepted::class);
        Notification::assertNotSentTo($customer->user, OfferAccepted::class);
        $sent = Notification::sent($selected->craftman->user, OfferAccepted::class)->sole();
        $data = $sent->toDatabase($selected->craftman->user);
        $this->assertSame('offer_accepted', $data['event']);
        $this->assertSame($order->id, $data['order_id']);
        $this->assertSame($selected->id, $data['offer_id']);
        $this->assertSame($job->id, $data['service_job_id']);
        $this->assertSame($order->title, $data['order_title']);
        $this->assertSame(number_format((float) $selected->price, 2, '.', ''), $data['agreed_price']);
        $this->assertSame('herafy.craftman.jobs.show', $data['destination']);
    }

    public function test_database_notification_targets_craftman_user_and_begins_unread(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();
        $this->accept($customer, $order, $selected);

        $notification = $selected->craftman->user->notifications()->sole();
        $this->assertNull($notification->read_at);
        $this->assertSame($selected->craftman->user->id, $notification->notifiable_id);
        $this->assertSame($selected->craftman->user::class, $notification->notifiable_type);
    }

    public function test_failed_service_job_creation_creates_no_notification(): void
    {
        Notification::fake();
        [$customer, $order, $selected] = $this->acceptanceScenario();
        DB::listen(function (QueryExecuted $query): void {
            if (str_contains(strtolower($query->sql), 'insert into "service_jobs"')) {
                throw new RuntimeException('Simulated ServiceJob failure.');
            }
        });
        $this->withoutExceptionHandling();

        try {
            $this->accept($customer, $order, $selected);
            $this->fail('Expected ServiceJob creation failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated ServiceJob failure.', $exception->getMessage());
        }

        Notification::assertNothingSent();
        $this->assertSame('pending', $selected->fresh()->status);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_repeated_acceptance_does_not_create_duplicate_notification(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();
        $this->accept($customer, $order, $selected)->assertRedirect();
        $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $selected]))
            ->assertConflict();

        $this->assertSame(1, $selected->craftman->user->notifications()->count());
        $this->assertSame(1, ServiceJob::count());
    }

    public function test_craftman_navbar_shows_unread_badge_and_notification(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();
        $this->accept($customer, $order, $selected);

        $this->actingAs($selected->craftman->user, 'craftman')->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee('data-testid="notification-unread-count">1<', false)
            ->assertSee('تم قبول عرضك')
            ->assertSee($order->title)
            ->assertSee('عرض كل الإشعارات');
    }

    public function test_craftman_list_is_scoped_and_newest_first(): void
    {
        [$customer, $firstOrder, $firstOffer] = $this->acceptanceScenario();
        $otherCraftman = Craftman::factory()->create();
        $firstOrder->update(['title' => 'الطلب الأقدم']);
        $this->accept($customer, $firstOrder, $firstOffer);
        $this->travel(1)->second();
        [$newCustomer, $newOrder, $newOffer] = $this->acceptanceScenario($firstOffer->craftman);
        $newOrder->update(['title' => 'الطلب الأحدث']);
        $this->accept($newCustomer, $newOrder, $newOffer);
        [$otherCustomer, $otherOrder, $otherOffer] = $this->acceptanceScenario($otherCraftman);
        $otherOrder->update(['title' => 'طلب حرفي آخر']);
        $this->accept($otherCustomer, $otherOrder, $otherOffer);

        $this->actingAs($firstOffer->craftman->user, 'craftman')->get(route('herafy.craftman.notifications.index'))
            ->assertOk()
            ->assertSeeInOrder(['الطلب الأحدث', 'الطلب الأقدم'])
            ->assertDontSee('طلب حرفي آخر');
    }

    public function test_craftman_can_read_own_notification_and_not_another_craftmans(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();
        $this->accept($customer, $order, $selected);
        $notification = $selected->craftman->user->notifications()->sole();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($otherCraftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);

        $this->actingAs($selected->craftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.craftman.jobs.show', ServiceJob::sole()));
        $this->assertNotNull($notification->fresh()->read_at);
        $this->get(route('herafy.craftman.dashboard'))->assertDontSee('data-testid="notification-unread-count"', false);
    }

    public function test_mark_all_affects_only_authenticated_craftman(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();
        $this->accept($customer, $order, $selected);
        [$otherCustomer, $otherOrder, $otherOffer] = $this->acceptanceScenario();
        $this->accept($otherCustomer, $otherOrder, $otherOffer);
        $own = $selected->craftman->user->notifications()->sole();
        $other = $otherOffer->craftman->user->notifications()->sole();

        $this->actingAs($selected->craftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read-all'))
            ->assertRedirect();

        $this->assertNotNull($own->fresh()->read_at);
        $this->assertNull($other->fresh()->read_at);
    }

    public function test_guest_and_customer_cannot_access_craftman_notification_routes(): void
    {
        [$customer, $order, $selected] = $this->acceptanceScenario();
        $this->accept($customer, $order, $selected);
        $notification = $selected->craftman->user->notifications()->sole();

        $this->get(route('herafy.craftman.notifications.index'))->assertRedirect(route('login'));
        $this->patch(route('herafy.craftman.notifications.read', $notification->id))->assertRedirect(route('login'));
        $this->patch(route('herafy.craftman.notifications.read-all'))->assertRedirect(route('login'));

        $this->actingAs($customer->user, 'customer')->get(route('herafy.craftman.notifications.index'))->assertRedirect(route('login'));
        $this->patch(route('herafy.craftman.notifications.read', $notification->id))->assertRedirect(route('login'));
        $this->patch(route('herafy.craftman.notifications.read-all'))->assertRedirect(route('login'));
    }

    private function acceptanceScenario(?Craftman $selectedCraftman = null): array
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id, 'status' => 'pending']);
        $selectedCraftman ??= Craftman::factory()->create();
        $selectedCraftman->update(['profession_id' => $order->profession_id]);
        $selected = Offer::factory()->create(['order_id' => $order->id, 'craftman_id' => $selectedCraftman->id, 'status' => 'pending']);
        $otherCraftman = Craftman::factory()->create(['profession_id' => $order->profession_id]);
        $other = Offer::factory()->create(['order_id' => $order->id, 'craftman_id' => $otherCraftman->id, 'status' => 'pending']);

        return [$customer, $order, $selected, $other];
    }

    private function accept(Customer $customer, Order $order, Offer $offer)
    {
        $response = $this->actingAs($customer->user, 'customer')
            ->patch(route('herafy.customer.orders.offers.accept', [$order, $offer]));
        auth('customer')->logout();

        return $response;
    }
}
