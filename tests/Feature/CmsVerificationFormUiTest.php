<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsVerificationFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_management_form_renders_safe_context_statuses_document_and_put_actions(): void
    {
        $verification = Verification::factory()->create(['status' => 'pending']);
        $verification->craftman->user->update(['name' => 'حرفي إدارة التوثيق']);
        $content = $this->actingAs($this->adminWith(['verifications.update']), 'admin')
            ->get(route('verifications.edit', $verification))->assertOk()->getContent();

        $this->assertStringContainsString('حرفي إدارة التوثيق', $content);
        $this->assertStringNotContainsString('رقم الحرفي', $content);
        foreach (['قيد المراجعة', 'معتمد', 'مرفوض'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
        $this->assertStringContainsString(asset('storage/'.$verification->document_path), $content);
        $this->assertStringContainsString('action="'.route('verifications.update', $verification).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertStringContainsString('type="submit"', $content);
        $this->assertStringContainsString('href="'.route('verifications.show', $verification).'"', $content);
        $this->assertSame(1, substr_count($content, 'class="cms-required"'));
    }

    public function test_invalid_status_renders_summary_field_error_and_old_input(): void
    {
        $verification = Verification::factory()->create(['status' => 'pending']);
        $this->actingAs($this->adminWith(['verifications.update']), 'admin');

        $this->from(route('verifications.edit', $verification))
            ->put(route('verifications.update', $verification), ['status' => 'invalid'])
            ->assertRedirect(route('verifications.edit', $verification))
            ->assertSessionHasErrors('status');

        $this->get(route('verifications.edit', $verification))->assertOk()
            ->assertSee('يرجى مراجعة الحقول التالية وتصحيح الأخطاء.')
            ->assertSee('name="status" class="form-select is-invalid"', false)
            ->assertSee('invalid-feedback', false);
    }

    public function test_admin_verification_create_page_was_not_introduced(): void
    {
        $this->actingAs($this->adminWith(['verifications.update']), 'admin')
            ->get('/cms/admin/verifications/create')
            ->assertNotFound();

        $this->assertFalse(app('router')->getRoutes()->hasNamedRoute('verifications.create'));
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'verification-form-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
