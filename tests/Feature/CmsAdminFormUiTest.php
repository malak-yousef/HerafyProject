<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsAdminFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_renders_safe_semantic_fields_and_allowed_roles(): void
    {
        Role::findOrCreate('content-admin', 'admin');
        Role::findOrCreate('super-admin', 'admin');
        $actor = $this->adminWithPermission('admins.create');

        $response = $this->actingAs($actor, 'admin')->get(route('admins.create'));
        $content = $response->assertOk()->getContent();

        foreach (['name', 'user_name', 'email', 'mobile', 'address', 'date', 'gender', 'status', 'password', 'password_confirmation', 'role_name', 'image'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $content);
        }
        foreach (['name', 'user_name', 'email', 'status', 'password', 'password_confirmation', 'role_name'] as $field) {
            $this->assertMatchesRegularExpression('/name="'.preg_quote($field, '/').'"[^>]*required/', $content);
        }

        $this->assertStringContainsString('action="'.route('admins.store').'" method="POST"', $content);
        $this->assertStringContainsString('name="_token"', $content);
        $this->assertStringContainsString('type="submit"', $content);
        $this->assertStringContainsString('href="'.route('admins.index').'"', $content);
        $this->assertStringContainsString('value="content-admin"', $content);
        $this->assertStringNotContainsString('value="super-admin"', $content);
        $this->assertStringNotContainsString('name="actor_type"', $content);
        $this->assertStringNotContainsString('name="actor_id"', $content);
        $this->assertStringNotContainsString('name="guard_name"', $content);
        $this->assertStringNotContainsString('name="permissions', $content);
        $this->assertMatchesRegularExpression('/name="password"[^>]*>/', $content);
        $this->assertStringNotContainsString('name="password" value=', $content);
    }

    public function test_super_admin_can_see_super_admin_as_create_only_role_option(): void
    {
        $super = $this->admin('super-admin');

        $this->actingAs($super, 'admin')->get(route('admins.create'))
            ->assertOk()
            ->assertSee('value="super-admin"', false)
            ->assertSee('مدير النظام الأعلى');
    }

    public function test_create_validation_renders_summary_field_errors_and_safe_old_input(): void
    {
        Role::findOrCreate('content-admin', 'admin');
        $actor = $this->adminWithPermission('admins.create');

        $response = $this->actingAs($actor, 'admin')
            ->from(route('admins.create'))
            ->post(route('admins.store'), [
                'name' => 'الاسم المحفوظ',
                'user_name' => 'old_admin',
                'email' => 'invalid-email',
                'status' => 'inactive',
                'password' => 'SecretPassword123',
                'password_confirmation' => 'different',
                'role_name' => 'content-admin',
            ]);

        $response->assertRedirect(route('admins.create'));
        $page = $this->get(route('admins.create'));
        $page->assertOk()
            ->assertSee('يرجى مراجعة الحقول التالية وتصحيح الأخطاء.')
            ->assertSee('الاسم المحفوظ')
            ->assertSee('old_admin')
            ->assertSee('value="inactive" selected', false)
            ->assertSee('is-invalid', false)
            ->assertDontSee('SecretPassword123');
    }

    public function test_edit_preselects_values_and_displays_roles_read_only(): void
    {
        $actor = $this->adminWithPermission('admins.update');
        $target = $this->admin('content-admin', [
            'name' => 'Current Admin',
            'user_name' => 'current_admin',
            'email' => 'current@example.com',
            'mobile' => '0591111111',
            'address' => 'Ramallah',
            'date' => '1990-05-04',
            'gender' => 'female',
            'status' => 'inactive',
            'image' => 'admin.jpg',
        ]);
        $target->assignRole(Role::findOrCreate('auditor', 'admin'));

        $response = $this->actingAs($actor, 'admin')->get(route('admins.edit', $target));
        $content = $response->assertOk()->getContent();

        foreach (['Current Admin', 'current_admin', 'current@example.com', '0591111111', 'Ramallah', '1990-05-04'] as $value) {
            $this->assertStringContainsString($value, $content);
        }
        $this->assertStringContainsString('value="female" selected', $content);
        $this->assertStringContainsString('value="inactive" selected', $content);
        $this->assertStringContainsString('data-testid="admin-readonly-roles"', $content);
        $this->assertStringContainsString('content admin', $content);
        $this->assertStringContainsString('auditor', $content);
        $this->assertStringNotContainsString('name="role_name"', $content);
        $this->assertStringNotContainsString('name="actor_type"', $content);
        $this->assertStringNotContainsString('name="actor_id"', $content);
        $this->assertStringNotContainsString($target->password, $content);
        $this->assertStringContainsString('اترك كلمة المرور فارغة للاحتفاظ بكلمة المرور الحالية.', $content);
        $this->assertStringContainsString('storage/images/admin/admin.jpg', $content);
        $this->assertStringContainsString('action="'.route('admins.update', $target).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertStringContainsString('name="_token"', $content);
    }

    public function test_edit_renders_all_canonical_arabic_status_labels_and_blank_passwords(): void
    {
        $actor = $this->adminWithPermission('admins.update');
        $target = $this->admin();
        $content = $this->actingAs($actor, 'admin')->get(route('admins.edit', $target))->assertOk()->getContent();

        foreach (['نشط', 'غير نشط', 'قيد الانتظار', 'محظور'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
        $this->assertStringNotContainsString('in_active', $content);
        $this->assertSame(1, substr_count($content, 'name="password"'));
        $this->assertSame(1, substr_count($content, 'name="password_confirmation"'));
        $this->assertStringNotContainsString('name="password" value=', $content);
        $this->assertStringNotContainsString('name="password_confirmation" value=', $content);
    }

    private function adminWithPermission(string $permission): User
    {
        $role = Role::findOrCreate('operator-'.uniqid(), 'admin');
        $role->givePermissionTo(Permission::findOrCreate($permission, 'admin'));

        return $this->admin($role->name);
    }

    private function admin(string $roleName = 'content-admin', array $attributes = []): User
    {
        $admin = User::factory()->create(array_merge([
            'actor_type' => null,
            'actor_id' => null,
            'status' => 'active',
            'password' => Hash::make('CurrentPassword123'),
        ], $attributes));
        $admin->assignRole(Role::findOrCreate($roleName, 'admin'));

        return $admin;
    }
}
