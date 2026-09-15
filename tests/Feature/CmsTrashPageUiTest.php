<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsTrashPageUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_trash_index_shows_only_trashed_records_with_fifteen_item_pagination(): void
    {
        $trashed = Category::factory()->count(16)->create();
        $trashed->each->delete();
        $active = Category::factory()->create(['name' => 'Active category']);

        $response = $this->actingAs($this->adminWith(['categories.view', 'categories.delete']), 'admin')
            ->get(route('categories_trashed'));

        $response->assertOk()
            ->assertViewHas('categories', fn ($categories) => $categories->perPage() === 15 && $categories->total() === 16)
            ->assertSee('سلة المحذوفات')
            ->assertSee('تاريخ الحذف')
            ->assertDontSee($active->name);
    }

    public function test_empty_trash_uses_shared_arabic_empty_state(): void
    {
        $this->actingAs($this->adminWith(['cities.view']), 'admin')
            ->get(route('cities_trashed'))
            ->assertOk()
            ->assertSee('لا توجد عناصر محذوفة.');
    }

    public function test_all_existing_trash_pages_render_through_the_shared_structure(): void
    {
        $routes = [
            'admins_trashed' => 'admins.view',
            'areas_trashed' => 'areas.view',
            'categories_trashed' => 'categories.view',
            'cities_trashed' => 'cities.view',
            'craftmen_trashed' => 'craftmen.view',
            'customers_trashed' => 'customers.view',
            'offers_trashed' => 'offers.view',
            'orders_trashed' => 'orders.view',
            'order_images_trashed' => 'order-images.view',
            'portfolios_trashed' => 'portfolios.view',
            'portfolio_images_trashed' => 'portfolio-images.view',
            'professions_trashed' => 'professions.view',
            'reports.trashed' => 'reports.view',
            'reviews_trashed' => 'reviews.view',
            'service-jobs.trashed' => 'service-jobs.view',
            'settings.trashed' => 'settings.manage',
            'verifications.trashed' => 'verifications.view',
        ];
        $admin = $this->adminWith(array_values($routes));
        $this->actingAs($admin, 'admin');

        foreach (array_keys($routes) as $routeName) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSee('لا توجد عناصر محذوفة.');
        }
    }

    public function test_trash_actions_use_csrf_protected_mutation_forms_and_shared_script_once(): void
    {
        $category = Category::factory()->create();
        $category->delete();

        $content = $this->actingAs($this->adminWith(['categories.view', 'categories.delete']), 'admin')
            ->get(route('categories_trashed'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('action="'.route('categories_restore', $category).'"', $content);
        $this->assertStringContainsString('value="PATCH"', $content);
        $this->assertStringContainsString('action="'.route('categories_force', $category).'"', $content);
        $this->assertStringContainsString('value="DELETE"', $content);
        $this->assertStringContainsString('name="_token"', $content);
        $this->assertSame(1, substr_count($content, 'cms/js/trash-actions.js'));
        $this->assertStringContainsString('data-trash-select-all', $content);
        $this->assertStringContainsString('name="ids[]"', $content);
    }

    public function test_bulk_force_delete_affects_only_selected_trashed_records(): void
    {
        [$selected, $unselected] = Category::factory()->count(2)->create();
        $active = Category::factory()->create();
        $selected->delete();
        $unselected->delete();

        $this->actingAs($this->adminWith(['categories.delete']), 'admin')
            ->delete(route('categories_forceAll'), ['ids' => [$selected->id, $active->id]])
            ->assertRedirect();

        $this->assertDatabaseMissing('categories', ['id' => $selected->id]);
        $this->assertSoftDeleted($unselected);
        $this->assertDatabaseHas('categories', ['id' => $active->id, 'deleted_at' => null]);
    }

    public function test_bulk_force_delete_requires_a_non_empty_selection(): void
    {
        $category = Category::factory()->create();
        $category->delete();

        $this->actingAs($this->adminWith(['categories.delete']), 'admin')
            ->from(route('categories_trashed'))
            ->delete(route('categories_forceAll'))
            ->assertRedirect(route('categories_trashed'))
            ->assertSessionHasErrors('ids');

        $this->assertSoftDeleted($category);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'trash-ui-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
