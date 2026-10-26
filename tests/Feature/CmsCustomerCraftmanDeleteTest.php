<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsCustomerCraftmanDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_html_delete_soft_deletes_and_redirects_with_flash(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->adminWith('customers.delete'), 'admin')
            ->delete(route('customers.destroy', $customer));

        $response->assertRedirect(route('customers.index'))
            ->assertSessionHas('success', 'تم حذف العميل بنجاح.');
        $this->assertSoftDeleted($customer);
    }

    public function test_craftman_html_delete_soft_deletes_and_redirects_with_flash(): void
    {
        $craftman = Craftman::factory()->create();

        $response = $this->actingAs($this->adminWith('craftmen.delete'), 'admin')
            ->delete(route('craftmen.destroy', $craftman));

        $response->assertRedirect(route('craftmen.index'))
            ->assertSessionHas('success', 'تم حذف الحرفي بنجاح.');
        $this->assertSoftDeleted($craftman);
    }

    public function test_json_delete_keeps_json_redirect_payload_for_both_resources(): void
    {
        $customer = Customer::factory()->create();
        $craftman = Craftman::factory()->create();
        $this->actingAs($this->adminWith(['customers.delete', 'craftmen.delete']), 'admin');

        $this->deleteJson(route('customers.destroy', $customer))
            ->assertOk()->assertJsonPath('redirect', route('customers.index'));
        $this->deleteJson(route('craftmen.destroy', $craftman))
            ->assertOk()->assertJsonPath('redirect', route('craftmen.index'));
    }

    public function test_unauthorized_delete_is_blocked_and_records_remain_active(): void
    {
        $customer = Customer::factory()->create();
        $craftman = Craftman::factory()->create();
        $this->actingAs($this->adminWith([]), 'admin');

        $this->delete(route('customers.destroy', $customer))->assertForbidden();
        $this->delete(route('craftmen.destroy', $craftman))->assertForbidden();
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('craftmen', ['id' => $craftman->id, 'deleted_at' => null]);
    }

    public function test_normal_delete_routes_are_delete_only_and_not_force_delete(): void
    {
        foreach (['customers.destroy', 'craftmen.destroy'] as $name) {
            $route = app('router')->getRoutes()->getByName($name);
            $this->assertInstanceOf(Route::class, $route);
            $this->assertContains('DELETE', $route->methods());
            $this->assertNotContains('GET', $route->methods());
        }
    }

    public function test_customer_force_delete_removes_only_the_selected_trashed_customer(): void
    {
        $selected = Customer::factory()->create();
        $unselected = Customer::factory()->create();
        $selectedUserId = $selected->user->id;
        $selected->delete();
        $unselected->delete();

        $this->actingAs($this->adminWith('customers.delete'), 'admin')
            ->delete(route('customers_force', $selected))
            ->assertRedirect(route('customers_trashed'))
            ->assertSessionHas('success', 'تم حذف العميل بشكل دائم.');

        $this->assertDatabaseMissing('customers', ['id' => $selected->id]);
        $this->assertDatabaseMissing('users', ['id' => $selectedUserId]);
        $this->assertSoftDeleted($unselected);
    }

    public function test_customer_bulk_force_delete_removes_only_selected_trashed_customers(): void
    {
        $selected = Customer::factory()->create();
        $unselected = Customer::factory()->create();
        $active = Customer::factory()->create();
        $selectedUserId = $selected->user->id;
        $selected->delete();
        $unselected->delete();

        $this->actingAs($this->adminWith('customers.delete'), 'admin')
            ->delete(route('customers_forceAll'), ['ids' => [$selected->id, $active->id]])
            ->assertRedirect(route('customers_trashed'))
            ->assertSessionHas('success', 'تم حذف العملاء المحددين بشكل دائم.');

        $this->assertDatabaseMissing('customers', ['id' => $selected->id]);
        $this->assertDatabaseMissing('users', ['id' => $selectedUserId]);
        $this->assertSoftDeleted($unselected);
        $this->assertDatabaseHas('customers', ['id' => $active->id, 'deleted_at' => null]);
    }

    private function adminWith(array|string $permissions): User
    {
        $permissions = (array) $permissions;
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'customer-craftman-delete-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
