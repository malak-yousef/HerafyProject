<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsOrderFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_uses_shared_ux_and_preserves_old_values_and_errors(): void
    {
        $customer = Customer::factory()->create();
        $customer->user->update(['name' => 'عميل مقروء']);
        $profession = Profession::factory()->create(['name' => 'سباكة']);
        $city = City::factory()->create(['name' => 'غزة']);
        $area = Area::factory()->create(['city_id' => $city->id, 'name' => 'الرمال']);
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag(['area_id' => ['المنطقة غير متوافقة.']]));

        $content = $this->actingAs($this->adminWith('orders.create'), 'admin')
            ->withSession([
                '_old_input' => [
                    'customer_id' => $customer->id, 'profession_id' => $profession->id,
                    'city_id' => $city->id, 'area_id' => $area->id,
                    'title' => 'عنوان قديم', 'description' => 'وصف قديم',
                    'budget' => '175.5', 'service_date' => '2027-02-03',
                ],
                'errors' => $errors,
            ])->get(route('orders.create'))->assertOk()->getContent();

        $this->assertStringContainsString('method="POST"', $content);
        $this->assertStringContainsString('name="_token"', $content);
        foreach (['customer_id', 'profession_id', 'city_id', 'area_id', 'title', 'description', 'budget', 'service_date'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $content);
        }
        $this->assertStringContainsString('عميل مقروء', $content);
        $this->assertStringContainsString('سباكة', $content);
        $this->assertStringContainsString('value="'.$area->id.'" data-city="'.$city->id.'" selected', $content);
        $this->assertStringContainsString('value="عنوان قديم"', $content);
        $this->assertStringContainsString('وصف قديم', $content);
        $this->assertStringContainsString('value="175.5"', $content);
        $this->assertStringContainsString('value="2027-02-03"', $content);
        $this->assertStringContainsString('cms-form-errors-summary', $content);
        $this->assertStringContainsString('is-invalid', $content);
        $this->assertStringContainsString('المنطقة غير متوافقة.', $content);
        $this->assertStringContainsString('name="status"', $content);
        foreach (['قيد الانتظار', 'مقبول', 'قيد التنفيذ', 'مكتمل', 'ملغى'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
    }

    public function test_edit_form_preselects_model_values_and_preserves_put_and_cancel_semantics(): void
    {
        $order = Order::factory()->create([
            'title' => 'طلب أصلي', 'description' => 'وصف أصلي', 'budget' => 250,
            'service_date' => '2027-04-05', 'status' => 'in_progress',
        ]);

        $content = $this->actingAs($this->adminWith('orders.update'), 'admin')
            ->get(route('orders.edit', $order))->assertOk()->getContent();

        foreach ([$order->customer_id, $order->profession_id, $order->city_id] as $id) {
            $this->assertStringContainsString('value="'.$id.'" selected', $content);
        }
        $this->assertStringContainsString('value="'.$order->area_id.'" data-city="'.$order->city_id.'" selected', $content);
        $this->assertStringContainsString('value="طلب أصلي"', $content);
        $this->assertStringContainsString('وصف أصلي', $content);
        $this->assertStringContainsString('value="250.00"', $content);
        $this->assertStringContainsString('value="2027-04-05"', $content);
        $this->assertStringContainsString('value="in_progress" selected', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertStringContainsString('href="'.route('orders.index').'"', $content);
        foreach (['قيد الانتظار', 'مقبول', 'قيد التنفيذ', 'مكتمل', 'ملغى'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
    }

    public function test_edit_old_values_override_every_corresponding_model_value_and_location_script_is_once(): void
    {
        $order = Order::factory()->create();
        $customer = Customer::factory()->create();
        $profession = Profession::factory()->create();
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);

        $content = $this->actingAs($this->adminWith('orders.update'), 'admin')
            ->withSession(['_old_input' => [
                'customer_id' => $customer->id, 'profession_id' => $profession->id,
                'city_id' => $city->id, 'area_id' => $area->id,
                'title' => 'عنوان بديل', 'description' => 'وصف بديل',
                'address' => 'عنوان موقع بديل', 'phone' => '0590000000',
                'budget' => '333', 'service_date' => '2027-06-07', 'status' => 'cancelled',
            ]])->get(route('orders.edit', $order))->assertOk()->getContent();

        foreach ([$customer->id, $profession->id, $city->id] as $id) {
            $this->assertStringContainsString('value="'.$id.'" selected', $content);
        }
        $this->assertStringContainsString('value="'.$area->id.'" data-city="'.$city->id.'" selected', $content);
        foreach (['عنوان بديل', 'وصف بديل', 'عنوان موقع بديل', '0590000000', '333', '2027-06-07'] as $value) {
            $this->assertStringContainsString($value, $content);
        }
        $this->assertStringContainsString('value="cancelled" selected', $content);
        $this->assertSame(1, substr_count($content, 'cms/js/order-location.js'));
        $this->assertSame(1, substr_count($content, "initCmsOrderLocation('#cms-order-city', '#cms-order-area')"));
    }

    private function adminWith(string $permission): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'order-form-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));

        return $admin;
    }
}
