<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceTableUniquenessTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::factory()->admin('super-admin')->create(['status' => 'active']);
    }

    public function test_category_duplicate_name_is_rejected(): void
    {
        $admin = $this->createAdminUser();
        Category::factory()->create(['name' => 'سباكة متقدمة']);

        $this->actingAs($admin, 'admin')
            ->postJson('/cms/admin/categories', [
                'name' => 'سباكة متقدمة',
                'description' => 'وصف تصنيف السباكة',
            ])
            ->assertStatus(400)
            ->assertJson([
                'icon' => 'error',
                'title' => 'اسم التصنيف مستخدم مسبقاً',
            ]);
    }

    public function test_profession_duplicate_name_is_rejected(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::factory()->create(['name' => 'كهرباء منازل']);
        Profession::factory()->create(['name' => 'فني كهربائي', 'category_id' => $category->id]);

        $this->actingAs($admin, 'admin')
            ->postJson('/cms/admin/professions', [
                'name' => 'فني كهربائي',
                'description' => 'تركيبات وسحب كابلات',
                'category_id' => $category->id,
                'status' => 'active',
            ])
            ->assertStatus(400)
            ->assertJson([
                'icon' => 'error',
                'title' => 'اسم المهنة مستخدم مسبقاً',
            ]);
    }

    public function test_city_duplicate_name_is_rejected(): void
    {
        $admin = $this->createAdminUser();
        City::factory()->create(['name' => 'القدس الشريف']);

        $this->actingAs($admin, 'admin')
            ->postJson('/cms/admin/cities', [
                'name' => 'القدس الشريف',
            ])
            ->assertStatus(400)
            ->assertJson([
                'icon' => 'error',
                'title' => 'اسم المدينة مستخدم مسبقاً',
            ]);
    }

    public function test_area_duplicate_name_in_same_city_is_rejected(): void
    {
        $admin = $this->createAdminUser();
        $city = City::factory()->create(['name' => 'مدينة غزة']);
        Area::factory()->create(['name' => 'حي الرمال', 'city_id' => $city->id]);

        $this->actingAs($admin, 'admin')
            ->postJson('/cms/admin/areas', [
                'name' => 'حي الرمال',
                'city_id' => $city->id,
            ])
            ->assertStatus(400)
            ->assertJson([
                'icon' => 'error',
                'title' => 'اسم المنطقة مستخدم مسبقاً في هذه المدينة',
            ]);
    }

    public function test_area_same_name_in_different_city_is_allowed(): void
    {
        $admin = $this->createAdminUser();
        $city1 = City::factory()->create(['name' => 'المدينة الأولى']);
        $city2 = City::factory()->create(['name' => 'المدينة الثانية']);
        Area::factory()->create(['name' => 'حي النصر', 'city_id' => $city1->id]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson('/cms/admin/areas', [
                'name' => 'حي النصر',
                'city_id' => $city2->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('areas', [
            'name' => 'حي النصر',
            'city_id' => $city2->id,
        ]);
    }
}
