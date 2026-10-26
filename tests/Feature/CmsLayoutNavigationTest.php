<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsLayoutNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_shows_only_permitted_items_and_hides_empty_groups(): void
    {
        $response = $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.index'))
            ->assertOk();

        $response->assertSee('data-nav-group="marketplace"', false)
            ->assertSee('data-nav-item="orders"', false)
            ->assertDontSee('data-nav-item="offers"', false)
            ->assertDontSee('data-nav-group="users"', false)
            ->assertDontSee('order_images')
            ->assertDontSee('portfolio_images');
    }

    public function test_resource_routes_open_and_highlight_only_the_relevant_group(): void
    {
        $orders = $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.index'))
            ->assertOk();

        $orders->assertSee('nav-group open" data-nav-group="marketplace"', false)
            ->assertSee('data-nav-item="orders" class="active"', false)
            ->assertDontSee('data-nav-item="dashboard" class="nav-link active"', false);

        auth('admin')->logout();

        $reviews = $this->actingAs($this->adminWith(['reviews.view']), 'admin')
            ->get(route('reviews.index'))
            ->assertOk();

        $reviews->assertSee('nav-group open" data-nav-group="moderation"', false)
            ->assertSee('data-nav-item="reviews" class="active"', false);
    }

    public function test_header_renders_admin_identity_and_one_post_logout_without_profile_link(): void
    {
        $admin = $this->adminWith(['dashboard.view']);
        $admin->update(['name' => 'مدير الاختبار']);

        $content = $this->actingAs($admin, 'admin')->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('مدير الاختبار', $content);
        $this->assertSame(1, substr_count($content, 'id="logoutForm"'));
        $this->assertStringContainsString('action="'.route('logout').'" method="POST"', $content);
        $this->assertStringNotContainsString('admin.profile', $content);
        $this->assertStringNotContainsString('متصل الآن', $content);
    }

    public function test_dashboard_and_major_resource_breadcrumbs_render(): void
    {
        $dashboard = $this->actingAs($this->adminWith(['dashboard.view']), 'admin')
            ->get(route('dashboard'))
            ->assertOk();
        $dashboard->assertSee('data-testid="cms-breadcrumb"', false)->assertSee('لوحة التحكم');

        auth('admin')->logout();

        $orders = $this->actingAs($this->adminWith(['orders.view']), 'admin')
            ->get(route('orders.index'))
            ->assertOk();
        $orders->assertSee('data-testid="cms-breadcrumb"', false)
            ->assertSee('لوحة التحكم')
            ->assertSee('الطلبات');
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'layout-admin-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
