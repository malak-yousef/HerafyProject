<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_super_admin_can_access_representative_cms_sections(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin1@example.com')->firstOrFail();
        $this->actingAs($admin, 'admin');

        foreach (['/cms/admin', '/cms/admin/categories', '/cms/admin/roles', '/cms/admin/permissions', '/cms/admin/settings'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_regular_admin_without_permission_receives_forbidden(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('categories.index'))
            ->assertForbidden();
    }

    public function test_view_permission_allows_view_but_not_mutations(): void
    {
        $admin = $this->adminWith('categories.view');
        $category = Category::factory()->create();
        $this->actingAs($admin, 'admin');

        $this->get(route('categories.index'))->assertOk();
        $this->get(route('categories.create'))->assertForbidden();
        $this->putJson(route('categories.update', $category), $this->categoryPayload())->assertForbidden();
        $this->deleteJson(route('categories.destroy', $category))->assertForbidden();
    }

    public function test_create_permission_allows_creating_a_resource(): void
    {
        $this->actingAs($this->adminWith('categories.create'), 'admin')
            ->postJson(route('categories.store'), $this->categoryPayload())
            ->assertOk();

        $this->assertDatabaseHas('categories', ['name' => 'Secure Category']);
    }

    public function test_update_permission_allows_updating_a_resource(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->adminWith('categories.update'), 'admin')
            ->putJson(route('categories.update', $category), $this->categoryPayload())
            ->assertOk();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Secure Category']);
    }

    public function test_delete_permission_allows_deleting_a_resource(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->adminWith('categories.delete'), 'admin')
            ->deleteJson(route('categories.destroy', $category))
            ->assertOk();

        $this->assertSoftDeleted($category);
    }

    public function test_management_sections_require_their_specific_permission(): void
    {
        foreach (['roles.index' => 'roles.manage', 'permissions.index' => 'permissions.manage', 'settings.index' => 'settings.manage'] as $route => $permission) {
            $this->actingAs($this->admin(), 'admin')->get(route($route))->assertForbidden();
            auth('admin')->logout();
            $this->actingAs($this->adminWith($permission), 'admin')->get(route($route))->assertOk();
            auth('admin')->logout();
        }
    }

    public function test_customer_and_craftman_cannot_bypass_the_admin_boundary(): void
    {
        $permission = Permission::create(['name' => 'categories.view', 'guard_name' => 'admin']);
        $customer = Customer::factory()->create();
        $customer->user->permissions()->attach($permission);
        $this->actingAs($customer->user, 'customer')->get(route('categories.index'))->assertRedirect(route('login'));
        auth('customer')->logout();

        $craftman = Craftman::factory()->create();
        $craftman->user->permissions()->attach($permission);
        $this->actingAs($craftman->user, 'craftman')->get(route('categories.index'))->assertRedirect(route('login'));
    }

    public function test_permission_from_the_wrong_guard_does_not_authorize_admin(): void
    {
        $admin = $this->admin();
        $wrongGuardPermission = Permission::create(['name' => 'categories.view', 'guard_name' => 'web']);
        $admin->permissions()->attach($wrongGuardPermission);

        $this->actingAs($admin, 'admin')->get(route('categories.index'))->assertForbidden();
    }

    public function test_role_permission_assignment_rejects_cross_guard_permissions(): void
    {
        $manager = $this->adminWith('roles.manage');
        $role = Role::create(['name' => 'managed-admin', 'guard_name' => 'admin']);
        $wrongGuardPermission = Permission::create(['name' => 'categories.view', 'guard_name' => 'web']);

        $this->actingAs($manager, 'admin')
            ->postJson(route('roles.update-permissions', $role), [
                'permissions' => [$wrongGuardPermission->id],
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('permissions.0');

        $this->assertCount(0, $role->fresh()->permissions);
    }

    public function test_super_admin_bypass_does_not_depend_on_assigned_permissions(): void
    {
        $admin = $this->admin('super-admin');

        $this->assertDatabaseCount('permissions', 0);
        $this->actingAs($admin, 'admin')->get(route('categories.index'))->assertOk();
    }

    private function admin(string $roleName = 'regular-admin'): User
    {
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        return $admin;
    }

    private function adminWith(string $permissionName): User
    {
        $admin = $this->admin('regular-admin-'.str_replace('.', '-', $permissionName));
        $role = $admin->roles()->where('guard_name', 'admin')->firstOrFail();
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'admin']);
        $role->givePermissionTo($permission);

        return $admin;
    }

    private function categoryPayload(): array
    {
        return ['name' => 'Secure Category', 'description' => 'Authorized operation'];
    }
}
