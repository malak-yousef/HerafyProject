<?php

namespace Tests\Feature;

use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsServiceJobIndexUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_job_statuses_relationships_and_null_timestamps_render_safely(): void
    {
        $jobs = collect();
        foreach (['pending', 'in_progress', 'completed', 'cancelled'] as $status) {
            $jobs->push(ServiceJob::factory()->create(['status' => $status, 'started_at' => null, 'completed_at' => null]));
        }
        $job = $jobs->first();
        $job->order->update(['title' => 'طلب مهمة الواجهة']);
        $job->customer->user->update(['name' => 'عميل مهمة الواجهة']);
        $job->craftman->user->update(['name' => 'حرفي مهمة الواجهة']);

        $response = $this->actingAs($this->adminWith(['service-jobs.view']), 'admin')
            ->get(route('service-jobs.index'))->assertOk();

        foreach (['قيد الانتظار', 'قيد التنفيذ', 'مكتمل', 'ملغى'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('طلب مهمة الواجهة')
            ->assertSee('عميل مهمة الواجهة')
            ->assertSee('حرفي مهمة الواجهة')
            ->assertSee('لم يبدأ بعد')
            ->assertSee('—');
    }

    public function test_service_job_empty_states_and_all_filter_values_are_preserved(): void
    {
        $this->actingAs($this->adminWith(['service-jobs.view']), 'admin');

        $this->get(route('service-jobs.index'))->assertOk()->assertSee('لا توجد مهام خدمات حتى الآن.');

        $this->get(route('service-jobs.index', [
            'search' => 'غير-موجود',
            'status' => 'pending',
            'started_from' => '2026-01-01',
            'completed_to' => '2026-12-31',
            'sort' => 'started_desc',
        ]))->assertOk()
            ->assertSee('لا توجد نتائج مطابقة لخيارات البحث.')
            ->assertSee('إعادة تعيين الفلاتر')
            ->assertSee('value="pending" selected', false)
            ->assertSee('value="2026-01-01"', false)
            ->assertSee('value="2026-12-31"', false)
            ->assertSee('value="started_desc" selected', false)
            ->assertSee('href="'.route('service-jobs.index').'"', false);
    }

    public function test_service_job_actions_are_permission_aware_and_delete_is_a_mutation_form(): void
    {
        $job = ServiceJob::factory()->create();
        $content = $this->actingAs($this->adminWith(['service-jobs.view', 'service-jobs.update', 'service-jobs.delete']), 'admin')
            ->get(route('service-jobs.index'))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('service-jobs.show', $job).'"', $content);
        $this->assertStringContainsString('href="'.route('service-jobs.edit', $job).'"', $content);
        $this->assertStringContainsString('action="'.route('service-jobs.destroy', $job).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="DELETE"', $content);
        $this->assertSame(1, substr_count($content, 'href="'.route('service-jobs.destroy', $job).'"'));
        $this->assertStringNotContainsString('>بدء<', $content);
        $this->assertStringNotContainsString('>إكمال<', $content);

        auth('admin')->logout();
        $readOnly = $this->actingAs($this->adminWith(['service-jobs.view']), 'admin')
            ->get(route('service-jobs.index'))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('service-jobs.show', $job).'"', $readOnly);
        $this->assertStringNotContainsString('href="'.route('service-jobs.edit', $job).'"', $readOnly);
        $this->assertStringNotContainsString('action="'.route('service-jobs.destroy', $job).'"', $readOnly);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'service-jobs-ui-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
