<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SensitiveRouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_destructive_get_requests_are_rejected_without_modifying_data(): void
    {
        $category = Category::factory()->create();
        $category->delete();
        $admin = $this->adminWith('categories.delete');
        $this->actingAs($admin, 'admin');

        $this->get("/cms/admin/categories_restore/{$category->id}")->assertMethodNotAllowed();
        $this->assertSoftDeleted($category);

        $this->get("/cms/admin/categories_force/{$category->id}")->assertMethodNotAllowed();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_restore_and_force_delete_require_delete_permission(): void
    {
        $category = Category::factory()->create();
        $category->delete();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('categories_restore', $category))
            ->assertForbidden();
        $this->assertSoftDeleted($category);

        auth('admin')->logout();
        $this->actingAs($this->adminWith('categories.delete'), 'admin')
            ->patch(route('categories_restore', $category))
            ->assertRedirect();
        $this->assertNotSoftDeleted($category->fresh());

        $category->delete();
        $this->delete(route('categories_force', $category))->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_mutation_routes_are_csrf_protected(): void
    {
        $route = Route::getRoutes()->getByName('categories_force');

        $this->assertContains('web', $route->gatherMiddleware());
        $this->assertContains('DELETE', $route->methods());
        $this->assertNotContains('GET', $route->methods());
    }

    public function test_all_sensitive_custom_routes_use_mutation_verbs_and_delete_permission(): void
    {
        $sensitiveNames = [
            'admins_restore', 'admins_force', 'admins_forceAll',
            'areas_restore', 'areas_force', 'areas_forceAll',
            'categories_restore', 'categories_force', 'categories_forceAll',
            'cities_restore', 'cities_force', 'cities_forceAll',
            'craftmen_restore', 'craftmen_force_destroy', 'craftmen_destroy_all',
            'customers_restore', 'customers_force', 'customers_forceAll',
            'offers_restore', 'offers_force', 'offers_forceAll',
            'order_images_restore', 'order_images_force_destroy', 'order_images_destroy_all',
            'orders_restore', 'orders_force', 'orders_forceAll',
            'portfolio_images_restore', 'portfolio_images_force_destroy', 'portfolio_images_destroy_all',
            'portfolios_restore', 'portfolios_force_destroy', 'portfolios_destroy_all',
            'professions_restore', 'professions_force', 'professions_forceAll',
            'reviews_restore', 'reviews_force', 'reviews_forceAll',
        ];

        foreach ($sensitiveNames as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertNotContains('GET', $route->methods(), $name);
            $this->assertContains('auth:admin', $route->gatherMiddleware(), $name);
            $this->assertNotEmpty(array_filter(
                $route->gatherMiddleware(),
                fn (string $middleware) => str_starts_with($middleware, 'can:') && str_ends_with($middleware, '.delete')
            ), $name);
        }

        foreach (['service-jobs.restore', 'verifications.restore', 'settings.restore'] as $name) {
            $this->assertContains('PATCH', Route::getRoutes()->getByName($name)->methods(), $name);
        }
    }

    private function admin(): User
    {
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $role = Role::create(['name' => 'sensitive-route-admin-'.uniqid(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        return $admin;
    }

    private function adminWith(string $permission): User
    {
        $admin = $this->admin();
        $role = $admin->roles()->firstOrFail();
        $role->givePermissionTo(Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'admin',
        ]));

        return $admin;
    }
}
