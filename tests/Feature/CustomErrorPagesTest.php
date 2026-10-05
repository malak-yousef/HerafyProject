<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_page_renders_herafy_rtl_custom_view(): void
    {
        $response = $this->get('/non-existent-route-path-12345');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('الصفحة التي تبحث عنها غير موجودة');
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('حرفي');
    }

    public function test_403_page_renders_arabic_custom_view_instead_of_symfony_default(): void
    {
        $admin = $this->adminWith();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admins.index'));

        $response->assertForbidden();
        $response->assertSee('403');
        $response->assertSee('ليست لديك صلاحية الوصول إلى هذه الصفحة');
        $response->assertSee('dir="rtl"', false);
        $response->assertDontSee('Whoops');
    }

    public function test_4xx_page_never_exposes_the_raw_exception_message(): void
    {
        Route::middleware('web')->get('__test/4xx-probe', fn () => abort(409, 'Only pending orders can be cancelled.'));

        $response = $this->get('__test/4xx-probe');

        $response->assertStatus(409);
        $response->assertSee('409');
        $response->assertSee('تعارض في البيانات', false);
        $response->assertDontSee('Only pending orders can be cancelled.');
        $response->assertDontSee('Whoops');
    }

    public function test_4xx_page_does_not_call_the_exception_message(): void
    {
        $content = file_get_contents(resource_path('views/errors/4xx.blade.php'));

        $this->assertStringNotContainsString(
            'getMessage()',
            $content,
            'The 4xx page must not render the raw exception message, which can contain internal or English developer wording.'
        );
        $this->assertStringContainsString('$safeMessages', $content);
    }

    private function adminWith(): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'error-page-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        $role->givePermissionTo(Permission::firstOrCreate(['name' => 'customers.view', 'guard_name' => 'admin']));

        return $admin;
    }
}
