<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Craftman;
use App\Models\Portfolio;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsCatalogLocationUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_index_and_forms_use_shared_ui_validation_and_fifteen_item_pagination(): void
    {
        Category::factory()->count(16)->create();
        $response = $this->actingAs($this->adminWith(['categories.view', 'categories.update', 'categories.delete']), 'admin')->get(route('categories.index'))->assertOk();
        $response->assertViewHas('categories', fn ($items) => $items->count() === 15 && $items->perPage() === 15);
        $response->assertSee('cms-table-actions', false)->assertSee('method="POST"', false)->assertSee('name="_method" value="DELETE"', false);
        $this->assertStringContainsString('x-cms.empty-state', file_get_contents(resource_path('views/cms/category/index.blade.php')));

        $this->actingAs($this->adminWith(['categories.create']), 'admin')->from(route('categories.create'))->post(route('categories.store'), ['name' => 'x', 'description' => ''])->assertRedirect(route('categories.create'))->assertSessionHasErrors(['name', 'description']);
    }

    public function test_profession_index_and_edit_render_category_status_count_and_preserve_selection(): void
    {
        $category = Category::factory()->create(['name' => 'تصنيف واضح']);
        $profession = Profession::factory()->create(['category_id' => $category->id, 'status' => 'active']);
        Craftman::factory()->create(['profession_id' => $profession->id]);
        Profession::factory()->count(15)->create();

        $response = $this->actingAs($this->adminWith(['professions.view']), 'admin')->get(route('professions.index'))->assertOk();
        $response->assertViewHas('professions', fn ($items) => $items->perPage() === 15)->assertSee('تصنيف واضح')->assertSee('نشط')->assertSee('1');

        $content = $this->actingAs($this->adminWith(['professions.update']), 'admin')->get(route('professions.edit', $profession))->assertOk()->getContent();
        $this->assertStringContainsString('value="'.$category->id.'" selected', $content);
        $this->assertStringContainsString('form-errors-summary', file_get_contents(resource_path('views/cms/profession/_form.blade.php')));
    }

    public function test_city_and_area_indexes_render_counts_and_parent_names_with_standard_pagination(): void
    {
        $city = City::factory()->create(['name' => 'مدينة واضحة']);
        $area = Area::factory()->create(['city_id' => $city->id, 'name' => 'منطقة واضحة']);
        City::factory()->count(15)->create();

        $cityResponse = $this->actingAs($this->adminWith(['cities.view']), 'admin')->get(route('cities.index'))->assertOk();
        $cityResponse->assertViewHas('cities', fn ($items) => $items->perPage() === 15)->assertSee('مدينة واضحة')->assertSee('1');

        $areaContent = $this->actingAs($this->adminWith(['areas.view']), 'admin')->get(route('areas.index'))->assertOk()->getContent();
        $this->assertStringContainsString('منطقة واضحة', $areaContent);
        $this->assertStringContainsString('مدينة واضحة', $areaContent);

        $edit = $this->actingAs($this->adminWith(['areas.update']), 'admin')->get(route('areas.edit', $area))->assertOk()->getContent();
        $this->assertStringContainsString('value="'.$city->id.'" selected', $edit);
    }

    public function test_city_form_validation_uses_normal_error_redirect_without_changing_seed_rules(): void
    {
        $this->actingAs($this->adminWith(['cities.create']), 'admin')->from(route('cities.create'))->post(route('cities.store'), ['name' => 'غ'])->assertRedirect(route('cities.create'))->assertSessionHasErrors('name');
    }

    public function test_portfolio_index_and_forms_render_readable_craftman_and_preserve_real_fields(): void
    {
        $craftman = Craftman::factory()->create();
        $portfolio = Portfolio::factory()->create(['craftman_id' => $craftman->id, 'description' => str_repeat('وصف طويل ', 30), 'status' => true]);
        $portfolio->craftman->user->update(['name' => 'حرفي المعرض']);
        Portfolio::factory()->count(15)->create(['craftman_id' => $craftman->id]);

        $response = $this->actingAs($this->adminWith(['portfolios.view']), 'admin')->get(route('portfolios.index'))->assertOk();
        $response->assertViewHas('portfolios', fn ($items) => $items->count() === 15 && $items->perPage() === 15)->assertSee('حرفي المعرض')->assertSee('نشط')->assertDontSee($portfolio->description);

        $content = $this->actingAs($this->adminWith(['portfolios.update']), 'admin')->withSession(['_old_input' => ['title' => 'عنوان قديم', 'craftman_id' => $portfolio->craftman_id, 'status' => '0']])->get(route('portfolios.edit', $portfolio))->assertOk()->getContent();
        $this->assertStringContainsString('عنوان قديم', $content);
        $this->assertStringContainsString('value="'.$portfolio->craftman_id.'" selected', $content);
        $this->assertStringContainsString('value="0" selected', $content);
        $this->assertStringContainsString('name="portfolioCover_image"', $content);
        $this->assertStringNotContainsString('name="portfolio_images"', $content);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'catalog-ui-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
