<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsCustomerFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_has_expected_fields_required_markers_and_safe_actions(): void
    {
        $content = $this->actingAs($this->adminWith(['customers.create']), 'admin')
            ->get(route('customers.create'))->assertOk()->getContent();

        foreach (['name', 'user_name', 'email', 'password', 'status', 'gender', 'id_number', 'mobile', 'address', 'date', 'image'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $content);
        }
        $this->assertSame(7, substr_count($content, 'class="cms-required"'));
        $this->assertStringContainsString('action="'.route('customers.store').'" method="POST"', $content);
        $this->assertStringContainsString('type="submit"', $content);
        $this->assertStringContainsString('href="'.route('customers.index').'"', $content);
        $this->assertStringNotContainsString('name="actor_type"', $content);
        $this->assertStringNotContainsString('name="actor_id"', $content);
    }

    public function test_validation_errors_render_and_non_sensitive_old_input_is_retained(): void
    {
        $this->actingAs($this->adminWith(['customers.create']), 'admin');
        $password = 'never-render-this-password';

        $this->from(route('customers.create'))->post(route('customers.store'), [
            'name' => 'اسم العميل القديم',
            'user_name' => 'customer-old',
            'email' => 'invalid-email',
            'password' => $password,
            'status' => 'blocked',
            'gender' => 'male',
            'id_number' => '123456789',
        ])->assertRedirect(route('customers.create'))->assertSessionHasErrors('email');

        $response = $this->get(route('customers.create'))->assertOk();
        $response->assertSee('يرجى مراجعة الحقول التالية وتصحيح الأخطاء.')
            ->assertSee('value="اسم العميل القديم"', false)
            ->assertSee('value="invalid-email"', false)
            ->assertSee('value="blocked" selected', false)
            ->assertSee('name="email" value="invalid-email" class="form-control is-invalid"', false)
            ->assertDontSee($password);
    }

    public function test_edit_form_preselects_model_and_keeps_password_optional(): void
    {
        $customer = Customer::factory()->create(['id_number' => '987654321']);
        $customer->user->update(['name' => 'عميل حالي', 'email' => 'current@example.test', 'status' => 'inactive']);
        $content = $this->actingAs($this->adminWith(['customers.update']), 'admin')
            ->get(route('customers.edit', $customer))->assertOk()->getContent();

        $this->assertStringContainsString('value="عميل حالي"', $content);
        $this->assertStringContainsString('value="current@example.test"', $content);
        $this->assertStringContainsString('value="987654321"', $content);
        $this->assertStringContainsString('value="inactive" selected', $content);
        foreach (['نشط', 'غير نشط', 'محظور'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
        $this->assertStringContainsString('اتركها فارغة للاحتفاظ بكلمة المرور الحالية.', $content);
        $this->assertStringContainsString('action="'.route('customers.update', $customer).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertSame(6, substr_count($content, 'class="cms-required"'));
        $this->assertStringNotContainsString($customer->user->password, $content);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'customer-form-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
