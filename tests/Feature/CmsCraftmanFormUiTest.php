<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsCraftmanFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_renders_account_professional_and_image_fields_safely(): void
    {
        Profession::factory()->create(['name' => 'مهنة الاختبار']);
        $content = $this->actingAs($this->adminWith(['craftmen.create']), 'admin')
            ->get(route('craftmen.create'))->assertOk()->getContent();

        foreach (['name', 'user_name', 'email', 'password', 'status', 'gender', 'mobile', 'address', 'id_number', 'profession_id', 'experience_years', 'hourly_rate', 'daily_rate', 'description', 'image'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $content);
        }
        $this->assertSame(11, substr_count($content, 'class="cms-required"'));
        $this->assertStringContainsString('id="craftman-id-number"', $content);
        $this->assertStringContainsString('id="craftman-id-number" type="text" inputmode="numeric" name="id_number"', $content);
        $this->assertStringContainsString('مهنة الاختبار', $content);
        $this->assertStringNotContainsString('name="verification_status"', $content);
        $this->assertStringNotContainsString('name="rating"', $content);
        $this->assertStringNotContainsString('name="total_reviews"', $content);
        $this->assertStringNotContainsString('name="actor_type"', $content);
        $this->assertStringNotContainsString('name="actor_id"', $content);
    }

    public function test_validation_preserves_professional_old_input_but_never_password(): void
    {
        $profession = Profession::factory()->create();
        $this->actingAs($this->adminWith(['craftmen.create']), 'admin');
        $password = 'craftman-secret-not-rendered';

        $this->from(route('craftmen.create'))->post(route('craftmen.store'), [
            'name' => 'اسم الحرفي القديم',
            'user_name' => 'craftman-old',
            'email' => 'invalid-email',
            'password' => $password,
            'status' => 'active',
            'gender' => 'male',
            'id_number' => '123123123',
            'profession_id' => $profession->id,
            'experience_years' => 12,
            'hourly_rate' => 45.5,
            'daily_rate' => 250,
        ])->assertRedirect(route('craftmen.create'))->assertSessionHasErrors('email');

        $response = $this->get(route('craftmen.create'))->assertOk();
        $response->assertSee('يرجى مراجعة الحقول التالية وتصحيح الأخطاء.')
            ->assertSee('value="اسم الحرفي القديم"', false)
            ->assertSee('value="'.$profession->id.'" selected', false)
            ->assertSee('value="12"', false)
            ->assertSee('value="45.5"', false)
            ->assertSee('value="250"', false)
            ->assertSee('name="email" value="invalid-email" class="form-control is-invalid"', false)
            ->assertDontSee($password);
    }

    public function test_edit_preselects_profession_rates_status_and_optional_password(): void
    {
        $profession = Profession::factory()->create(['name' => 'النجارة']);
        $craftman = Craftman::factory()->create([
            'profession_id' => $profession->id,
            'id_number' => '456456456',
            'experience_years' => 9,
            'hourly_rate' => 55,
            'daily_rate' => 320,
        ]);
        $craftman->user->update(['name' => 'حرفي حالي', 'status' => 'blocked']);
        $content = $this->actingAs($this->adminWith(['craftmen.update']), 'admin')
            ->get(route('craftmen.edit', $craftman))->assertOk()->getContent();

        $this->assertStringContainsString('value="'.$profession->id.'" selected', $content);
        $this->assertStringContainsString('value="9"', $content);
        $this->assertStringContainsString('value="55.00"', $content);
        $this->assertStringContainsString('value="320.00"', $content);
        $this->assertStringContainsString('value="blocked" selected', $content);
        foreach (['نشط', 'غير نشط', 'محظور'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
        $this->assertStringContainsString('اتركها فارغة للاحتفاظ بكلمة المرور الحالية.', $content);
        $this->assertStringContainsString('action="'.route('craftmen.update', $craftman).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertSame(10, substr_count($content, 'class="cms-required"'));
        $this->assertStringNotContainsString($craftman->user->password, $content);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'craftman-form-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
