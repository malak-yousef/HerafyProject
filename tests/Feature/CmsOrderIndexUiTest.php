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

class CmsOrderIndexUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_order_statuses_render_as_standardized_arabic_badges(): void
    {
        $this->actingAs($this->adminWith(['orders.view']), 'admin');

        foreach (['pending', 'accepted', 'in_progress', 'completed', 'cancelled'] as $status) {
            Order::factory()->create(['status' => $status]);
        }

        $response = $this->get(route('orders.index'))->assertOk();

        foreach (['قيد الانتظار', 'مقبول', 'قيد التنفيذ', 'مكتمل', 'ملغى'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('class="badge bg-warning text-dark"', false)
            ->assertSee('class="badge bg-primary"', false)
            ->assertSee('class="badge bg-success"', false)
            ->assertSee('class="badge bg-danger"', false);
    }

    public function test_order_relationship_names_are_rendered_without_private_customer_contact_data(): void
    {
        $customer = Customer::factory()->create();
        $customer->user->update([
            'name' => 'عميل واجهة الطلبات',
            'email' => 'private-order@example.test',
            'mobile' => '0599999999',
        ]);
        $city = City::factory()->create(['name' => 'نابلس']);
        $area = Area::factory()->create(['city_id' => $city->id, 'name' => 'رفيديا']);
        $profession = Profession::factory()->create(['name' => 'نجارة']);
        Order::factory()->create([
            'customer_id' => $customer->id,
            'profession_id' => $profession->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
        ]);

        $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('عميل واجهة الطلبات')
            ->assertSee('نجارة')
            ->assertSee('نابلس')
            ->assertSee('رفيديا')
            ->assertDontSee('private-order@example.test')
            ->assertDontSee('0599999999');
    }

    public function test_empty_and_filtered_empty_states_render_correctly(): void
    {
        $this->actingAs($this->adminWith(['orders.view']), 'admin');

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('لا توجد طلبات حتى الآن.')
            ->assertDontSee('لا توجد نتائج مطابقة لخيارات البحث.');

        $this->get(route('orders.index', ['search' => 'نتيجة-غير-موجودة']))
            ->assertOk()
            ->assertSee('لا توجد نتائج مطابقة لخيارات البحث.')
            ->assertSee('إعادة تعيين الفلاتر')
            ->assertSee('href="'.route('orders.index').'"', false);
    }

    public function test_actions_follow_permissions_and_delete_is_never_a_get_link(): void
    {
        $order = Order::factory()->create();
        $admin = $this->adminWith(['orders.view', 'orders.update', 'orders.delete']);

        $content = $this->actingAs($admin, 'admin')->get(route('orders.index'))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('orders.show', $order).'"', $content);
        $this->assertStringContainsString('href="'.route('orders.edit', $order).'"', $content);
        $this->assertStringContainsString('action="'.route('orders.destroy', $order).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="DELETE"', $content);
        $this->assertSame(1, substr_count($content, 'href="'.route('orders.destroy', $order).'"'));
        $this->assertStringNotContainsString(route('offers.create', ['order_id' => $order->id]), $content);

        auth('admin')->logout();
        $readOnlyContent = $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('href="'.route('orders.show', $order).'"', $readOnlyContent);
        $this->assertStringNotContainsString('href="'.route('orders.edit', $order).'"', $readOnlyContent);
        $this->assertStringNotContainsString('action="'.route('orders.destroy', $order).'"', $readOnlyContent);

        auth('admin')->logout();
        $offersContent = $this->actingAs($this->adminWith(['orders.view', 'offers.create']), 'admin')
            ->get(route('orders.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('href="'.route('offers.create', ['order_id' => $order->id]).'"', $offersContent);
    }

    public function test_filter_values_and_pagination_links_preserve_query_parameters(): void
    {
        Order::factory()->count(16)->create(['status' => 'pending']);
        $query = ['status' => 'pending', 'sort' => 'oldest'];

        $response = $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.index', $query))
            ->assertOk();

        $response->assertSee('value="pending" selected', false)
            ->assertSee('value="oldest" selected', false)
            ->assertSee('status=pending', false)
            ->assertSee('sort=oldest', false);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'orders-ui-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
