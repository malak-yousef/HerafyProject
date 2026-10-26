<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsDeleteActionNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_delete_action_is_a_real_csrf_protected_delete_form(): void
    {
        $category = Category::factory()->create();
        $content = $this->actingAs($this->adminWith(['categories.view', 'categories.delete']), 'admin')
            ->get(route('categories.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('action="'.route('categories.destroy', $category).'"', $content);
        $this->assertStringContainsString('method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="DELETE"', $content);
        $this->assertStringContainsString('name="_token"', $content);
        $this->assertStringContainsString('type="submit" class="btn btn-sm btn-danger"', $content);
        $this->assertStringContainsString('هل أنت متأكد من حذف هذا العنصر؟', $content);
        $this->assertStringNotContainsString('onclick="performDestroy', $content);
    }

    public function test_normal_delete_soft_deletes_and_get_cannot_delete(): void
    {
        $category = Category::factory()->create();
        $this->actingAs($this->adminWith(['categories.view', 'categories.delete']), 'admin');

        $this->get(route('categories.destroy', $category))->assertOk();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);

        $this->delete(route('categories.destroy', $category))->assertSuccessful();
        $this->assertSoftDeleted($category);
    }

    public function test_view_only_user_sees_real_trash_link_but_not_delete_control(): void
    {
        $category = Category::factory()->create();
        $content = $this->actingAs($this->adminWith(['categories.view']), 'admin')
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee(route('categories_trashed'), false)
            ->assertSee('سلة المحذوفات')
            ->getContent();

        $this->assertStringNotContainsString('name="_method" value="DELETE"', $content);
        $this->assertStringNotContainsString('هل أنت متأكد من حذف هذا العنصر؟', $content);
    }

    public function test_resource_without_trash_route_does_not_receive_fake_navigation(): void
    {
        $this->actingAs($this->adminWith(['roles.manage']), 'admin')
            ->get(route('roles.index'))
            ->assertOk()
            ->assertDontSee('سلة المحذوفات');
    }

    public function test_trash_page_has_return_navigation_to_primary_index(): void
    {
        $this->actingAs($this->adminWith(['categories.view']), 'admin')
            ->get(route('categories_trashed'))
            ->assertOk()
            ->assertSee(route('categories.index'), false)
            ->assertSee('العودة للقائمة');
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'delete-action-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
