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

class CmsOrderLocationUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_exposes_city_mapping_and_preserves_old_location_values(): void
    {
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);

        $content = $this->actingAs($this->adminWith('orders.create'), 'admin')
            ->withSession(['_old_input' => ['city_id' => $city->id, 'area_id' => $area->id]])
            ->get(route('orders.create'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('id="cms-order-city"', $content);
        $this->assertStringContainsString('id="cms-order-area"', $content);
        $this->assertStringContainsString('data-city="'.$city->id.'"', $content);
        $this->assertStringContainsString('value="'.$city->id.'" selected', $content);
        $this->assertStringContainsString('value="'.$area->id.'" data-city="'.$city->id.'" selected', $content);
        $this->assertStringContainsString('order-location.js', $content);
    }

    public function test_edit_form_preselects_matching_city_and_area(): void
    {
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);
        $oldCity = City::factory()->create();
        $oldArea = Area::factory()->create(['city_id' => $oldCity->id]);
        $order = Order::factory()->create(['city_id' => $city->id, 'area_id' => $area->id]);

        $content = $this->actingAs($this->adminWith('orders.update'), 'admin')
            ->withSession(['_old_input' => ['city_id' => $oldCity->id, 'area_id' => $oldArea->id]])
            ->get(route('orders.edit', $order))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('value="'.$oldCity->id.'" selected', $content);
        $this->assertStringContainsString('value="'.$oldArea->id.'" data-city="'.$oldCity->id.'" selected', $content);
    }

    public function test_index_filter_retains_location_values_and_parent_mapping(): void
    {
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $city->id]);

        $content = $this->actingAs($this->adminWith('orders.view'), 'admin')
            ->get(route('orders.index', ['city_id' => $city->id, 'area_id' => $area->id]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('id="order-city"', $content);
        $this->assertStringContainsString('id="order-area"', $content);
        $this->assertStringContainsString('value="'.$city->id.'" selected', $content);
        $this->assertStringContainsString('value="'.$area->id.'" data-city="'.$city->id.'" selected', $content);
        $this->assertStringContainsString('initCmsOrderLocation', $content);
    }

    public function test_forged_mismatched_city_and_area_remains_rejected_server_side(): void
    {
        $city = City::factory()->create();
        $otherCity = City::factory()->create();
        $area = Area::factory()->create(['city_id' => $otherCity->id]);

        $response = $this->actingAs($this->adminWith('orders.create'), 'admin')
            ->from(route('orders.create'))
            ->post(route('orders.store'), [
                'customer_id' => Customer::factory()->create()->id,
                'profession_id' => Profession::factory()->create()->id,
                'city_id' => $city->id,
                'area_id' => $area->id,
                'title' => 'طلب اختبار',
                'description' => 'وصف الاختبار',
                'budget' => 100,
            ]);

        $response->assertSessionHasErrors('area_id');
        $this->assertDatabaseCount('orders', 0);
    }

    private function adminWith(string $permission): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'order-location-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));

        return $admin;
    }
}
