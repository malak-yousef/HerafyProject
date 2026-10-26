<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsOrderShowUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_detail_renders_core_fields_without_crashing(): void
    {
        $customer = Customer::factory()->create();
        $city = City::factory()->create(['name' => 'نابلس']);
        $area = Area::factory()->create(['city_id' => $city->id, 'name' => 'رفيديا']);
        $profession = Profession::factory()->create(['name' => 'نجارة']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'profession_id' => $profession->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
            'status' => 'pending',
            'title' => 'طلب تفاصيل الطلب',
        ]);

        $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('طلب #'.$order->id)
            ->assertSee('طلب تفاصيل الطلب')
            ->assertSee('نجارة')
            ->assertSee('نابلس')
            ->assertSee('رفيديا')
            ->assertSee('قيد الانتظار')
            ->assertSee('لا توجد صور مرفقة.')
            ->assertSee('لم يتم إنشاء عمل خدمة لهذا الطلب بعد.')
            ->assertSee('لا توجد عروض مقدمة على هذا الطلب بعد.');
    }

    public function test_order_detail_shows_arabic_status_for_every_supported_status(): void
    {
        $this->actingAs($this->adminWith(['orders.view']), 'admin');

        $expected = [
            'pending' => 'قيد الانتظار',
            'accepted' => 'مقبول',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغى',
        ];

        foreach ($expected as $status => $label) {
            $order = Order::factory()->create(['status' => $status]);

            $this->get(route('orders.show', $order))
                ->assertOk()
                ->assertSee($label);
        }
    }

    public function test_order_detail_hides_customer_contact_but_keeps_manageable_order_fields(): void
    {
        $customer = Customer::factory()->create();
        $customer->user->update([
            'name' => 'عميل صفحة التفاصيل',
            'email' => 'private-detail@example.test',
            'mobile' => '0598888888',
        ]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'phone' => '0566666666',
            'address' => 'عنوان الطلب',
        ]);

        $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('عميل صفحة التفاصيل')
            ->assertDontSee('private-detail@example.test')
            ->assertDontSee('0598888888')
            ->assertSee('0566666666')
            ->assertSee('عنوان الطلب');
    }

    public function test_order_detail_formats_budget_and_offers(): void
    {
        $this->actingAs($this->adminWith(['orders.view']), 'admin');

        $order = Order::factory()->create(['budget' => 1500]);

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('1,500.00 ₪');

        $nullBudget = Order::factory()->create(['budget' => null]);

        $this->get(route('orders.show', $nullBudget))
            ->assertOk()
            ->assertSee('غير محددة');
    }

    public function test_order_detail_edit_action_requires_update_permission(): void
    {
        $order = Order::factory()->create();

        $content = $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.show', $order))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('href="'.route('orders.edit', $order->id).'"', $content);
        $this->assertStringContainsString('href="'.route('orders.index').'"', $content);

        auth('admin')->logout();

        $editable = $this->actingAs($this->adminWith(['orders.view', 'orders.update']), 'admin')
            ->get(route('orders.show', $order))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('href="'.route('orders.edit', $order->id).'"', $editable);
    }

    public function test_order_detail_lists_offers_with_status_and_price(): void
    {
        $this->actingAs($this->adminWith(['orders.view']), 'admin');

        $order = Order::factory()->create(['status' => 'completed']);
        $craftman = Craftman::factory()->create();

        Offer::factory()->create([
            'order_id' => $order->id,
            'craftman_id' => $craftman->id,
            'price' => 250,
            'status' => 'pending',
            'message' => 'ملاحظة العرض',
        ]);

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('250.00 ₪')
            ->assertSee('قيد الانتظار')
            ->assertSee('ملاحظة العرض')
            ->assertDontSee('لا توجد عروض مقدمة على هذا الطلب بعد.');
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'order-show-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
