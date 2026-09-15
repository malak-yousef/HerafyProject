<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsOrderPhoneUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_admin_can_update_order_phone_with_other_fields(): void
    {
        $order = Order::factory()->create(['phone' => '0591111111']);

        $this->actingAs($this->adminWithUpdatePermission(), 'admin')
            ->put(route('orders.update', $order), $this->validPayload($order, [
                'phone' => '0592222222',
                'title' => 'عنوان محدث',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('orders.index'));

        $order->refresh();
        $this->assertSame('0592222222', $order->phone);
        $this->assertSame('عنوان محدث', $order->title);
    }

    public function test_order_phone_may_be_cleared(): void
    {
        $order = Order::factory()->create(['phone' => '0591111111']);

        $this->actingAs($this->adminWithUpdatePermission(), 'admin')
            ->put(route('orders.update', $order), $this->validPayload($order, ['phone' => null]))
            ->assertSessionHasNoErrors();

        $this->assertNull($order->fresh()->phone);
    }

    public function test_phone_longer_than_twenty_characters_is_rejected_without_changing_order(): void
    {
        $order = Order::factory()->create([
            'phone' => '0591111111',
            'title' => 'العنوان الأصلي',
        ]);

        $this->actingAs($this->adminWithUpdatePermission(), 'admin')
            ->from(route('orders.edit', $order))
            ->put(route('orders.update', $order), $this->validPayload($order, [
                'phone' => str_repeat('1', 21),
                'title' => 'عنوان يجب ألا يحفظ',
            ]))
            ->assertRedirect(route('orders.edit', $order))
            ->assertSessionHasErrors('phone');

        $order->refresh();
        $this->assertSame('0591111111', $order->phone);
        $this->assertSame('العنوان الأصلي', $order->title);
    }

    private function validPayload(Order $order, array $overrides = []): array
    {
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);

        return array_merge([
            'customer_id' => Customer::factory()->create()->id,
            'profession_id' => Profession::factory()->create()->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
            'title' => $order->title,
            'description' => $order->description,
            'phone' => $order->phone,
            'address' => $order->address,
            'service_date' => $order->service_date?->format('Y-m-d'),
            'budget' => $order->budget,
            'status' => $order->status,
        ], $overrides);
    }

    private function adminWithUpdatePermission(): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'order-phone-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => 'orders.update', 'guard_name' => 'admin']));

        return $admin;
    }
}
