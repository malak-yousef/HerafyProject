<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Profession;
use App\Notifications\NewEligibleOrder;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class NewEligibleOrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_order_notifies_each_active_matching_craftman_user_once_with_safe_payload(): void
    {
        Notification::fake();
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create(['status' => 'active', 'name' => 'نجارة']);
        $matching = Craftman::factory()->count(2)->create(['profession_id' => $profession->id]);
        $different = Craftman::factory()->create();

        $this->createOrder($customer, $this->validPayload($profession))->assertRedirect(route('request'));

        $order = Order::sole();
        foreach ($matching as $craftman) {
            Notification::assertSentToTimes($craftman->user, NewEligibleOrder::class, 1);
        }
        Notification::assertNotSentTo($different->user, NewEligibleOrder::class);
        Notification::assertNotSentTo($customer->user, NewEligibleOrder::class);

        $sent = Notification::sent($matching->first()->user, NewEligibleOrder::class)->sole();
        $data = $sent->toDatabase($matching->first()->user);
        $this->assertSame('new_eligible_order', $data['event']);
        $this->assertSame('طلب جديد متاح', $data['title']);
        $this->assertSame($order->id, $data['order_id']);
        $this->assertSame($order->title, $data['order_title']);
        $this->assertSame($profession->name, $data['profession_name']);
        $this->assertSame('250.00', $data['budget']);
        $this->assertSame($order->service_date->toDateString(), $data['service_date']);
        $this->assertSame('herafy.order.show', $data['destination']);
        $this->assertArrayNotHasKey('customer_id', $data);
        $this->assertArrayNotHasKey('address', $data);
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('email', $data);
    }

    public function test_database_notification_is_unread_and_refresh_does_not_resend_it(): void
    {
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create(['status' => 'active']);
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);

        $this->createOrder($customer, $this->validPayload($profession));
        $notification = $craftman->user->notifications()->sole();
        $this->assertNull($notification->read_at);
        $this->assertSame($craftman->user->id, $notification->notifiable_id);
        $this->assertSame($craftman->user::class, $notification->notifiable_type);

        $this->actingAs($customer->user, 'customer')->get(route('request'))->assertOk();
        $this->assertSame(1, $craftman->user->notifications()->count());
    }

    public function test_inactive_blocked_invalid_and_soft_deleted_craftmen_are_not_notified(): void
    {
        Notification::fake();
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create(['status' => 'active']);
        $inactive = Craftman::factory()->create(['profession_id' => $profession->id]);
        $inactive->user->update(['status' => 'inactive']);
        $blocked = Craftman::factory()->create(['profession_id' => $profession->id]);
        $blocked->user->update(['status' => 'blocked']);
        $invalid = Craftman::factory()->create(['profession_id' => $profession->id]);
        $invalidUser = $invalid->user;
        $invalidUser->update(['actor_id' => 999999]);
        $deleted = Craftman::factory()->create(['profession_id' => $profession->id]);
        $deletedUser = $deleted->user;
        $deleted->delete();

        $this->createOrder($customer, $this->validPayload($profession));

        Notification::assertNotSentTo($inactive->user, NewEligibleOrder::class);
        Notification::assertNotSentTo($blocked->user, NewEligibleOrder::class);
        Notification::assertNotSentTo($invalidUser, NewEligibleOrder::class);
        Notification::assertNotSentTo($deletedUser, NewEligibleOrder::class);
        Notification::assertNothingSent();
    }

    public function test_invalid_form_and_failed_image_transaction_create_no_notification(): void
    {
        Notification::fake();
        Storage::fake('public');
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create(['status' => 'active']);
        Craftman::factory()->create(['profession_id' => $profession->id]);

        $this->createOrder($customer, $this->validPayload($profession, ['title' => '']))
            ->assertSessionHasErrors('title');
        Notification::assertNothingSent();

        DB::listen(function (QueryExecuted $query): void {
            if (str_contains(strtolower($query->sql), 'insert into "order_images"')) {
                throw new RuntimeException('Simulated image persistence failure.');
            }
        });
        $this->withoutExceptionHandling();

        try {
            $this->createOrder($customer, $this->validPayload($profession, [
                'images' => [$this->fakeImage('rollback.png')],
            ]));
            $this->fail('Expected image persistence failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated image persistence failure.', $exception->getMessage());
        }

        Notification::assertNothingSent();
        $this->assertSame(0, Order::count());
    }

    public function test_craftman_ui_displays_notification_and_read_redirects_to_eligible_order(): void
    {
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create(['status' => 'active']);
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $this->createOrder($customer, $this->validPayload($profession));
        auth('customer')->logout();
        $order = Order::sole();
        $notification = $craftman->user->notifications()->sole();

        $this->actingAs($craftman->user, 'craftman')->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee('data-testid="notification-unread-count">1<', false)
            ->assertSee('طلب جديد متاح')
            ->assertSee($order->title);
        $this->get(route('herafy.craftman.notifications.index'))
            ->assertOk()
            ->assertSee($order->title);
        $this->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.order.show', $order));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_unavailable_order_is_not_exposed_and_another_craftman_cannot_read_notification(): void
    {
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create(['status' => 'active']);
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $otherCraftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $this->createOrder($customer, $this->validPayload($profession));
        auth('customer')->logout();
        $order = Order::sole();
        $notification = $craftman->user->notifications()->sole();

        $this->actingAs($otherCraftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);

        $order->update(['status' => 'cancelled']);
        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.craftman.notifications.index'));
        $this->assertNotNull($notification->fresh()->read_at);
        $this->get(route('herafy.order.show', $order))->assertNotFound();
    }

    private function createOrder(Customer $customer, array $payload)
    {
        return $this->actingAs($customer->user, 'customer')
            ->post(route('herafy.customer.orders.store'), $payload);
    }

    private function validPayload(Profession $profession, array $overrides = []): array
    {
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);

        return array_merge([
            'profession_id' => $profession->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
            'title' => 'إصلاح خزائن المطبخ',
            'description' => 'تحتاج أبواب الخزائن إلى إصلاح وضبط.',
            'budget' => 250,
            'service_date' => now()->addDay()->toDateString(),
        ], $overrides);
    }

    private function fakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true)
        );
    }
}
