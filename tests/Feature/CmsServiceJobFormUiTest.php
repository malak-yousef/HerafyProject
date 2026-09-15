<?php

namespace Tests\Feature;

use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsServiceJobFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_uses_readable_relationships_statuses_and_post_semantics(): void
    {
        $job = ServiceJob::factory()->create();
        $job->order->update(['title' => 'طلب دهان المنزل']);
        $job->customer->user->update(['name' => 'عميل المهمة']);
        $job->craftman->user->update(['name' => 'حرفي المهمة']);

        $content = $this->actingAs($this->adminWith('service-jobs.create'), 'admin')
            ->get(route('service-jobs.create'))->assertOk()->getContent();

        foreach (['order_id', 'offer_id', 'customer_id', 'craftman_id', 'status', 'started_at', 'completed_at'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $content);
        }
        foreach (['طلب دهان المنزل', 'عميل المهمة', 'حرفي المهمة', (string) $job->offer->price] as $label) {
            $this->assertStringContainsString($label, $content);
        }
        foreach (['قيد الانتظار', 'قيد التنفيذ', 'مكتمل', 'ملغي'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
        $this->assertStringContainsString('value="'.$job->order_id.'" data-customer-id="'.$job->customer_id.'"', $content);
        $this->assertStringContainsString('value="'.$job->offer_id.'" data-order-id="'.$job->order_id.'" data-craftman-id="'.$job->craftman_id.'"', $content);
        $this->assertSame(1, substr_count($content, 'cms/js/marketplace-relations.js'));
        $this->assertSame(1, substr_count($content, "CmsMarketplaceRelations.initServiceJob('#service-job-order', '#service-job-offer', '#service-job-customer', '#service-job-craftman')"));
        $this->assertStringNotContainsString('value="accepted"', $content);
        $this->assertStringContainsString('method="POST"', $content);
        $this->assertStringContainsString('name="_token"', $content);
    }

    public function test_edit_form_preselects_model_and_formats_timestamps_for_html(): void
    {
        $job = ServiceJob::factory()->completed()->create([
            'started_at' => '2027-03-04 09:15:00',
            'completed_at' => '2027-03-04 11:45:00',
        ]);

        $content = $this->actingAs($this->adminWith('service-jobs.update'), 'admin')
            ->get(route('service-jobs.edit', $job))->assertOk()->getContent();

        foreach ([$job->order_id, $job->offer_id, $job->customer_id, $job->craftman_id] as $id) {
            $this->assertStringContainsString('value="'.$id.'" selected', $content);
        }
        $this->assertStringContainsString('value="completed" selected', $content);
        $this->assertStringContainsString('value="2027-03-04T09:15"', $content);
        $this->assertStringContainsString('value="2027-03-04T11:45"', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertStringContainsString('href="'.route('service-jobs.index').'"', $content);
        $this->assertSame(1, substr_count($content, 'cms/js/marketplace-relations.js'));
    }

    public function test_old_values_and_conditional_timestamp_errors_render_beside_fields(): void
    {
        $job = ServiceJob::factory()->create();
        $other = ServiceJob::factory()->create();
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag([
            'started_at' => ['وقت البدء مطلوب.'],
            'completed_at' => ['وقت الإنجاز لا يمكن أن يسبق وقت البدء.'],
        ]));

        $content = $this->actingAs($this->adminWith('service-jobs.update'), 'admin')
            ->withSession([
                '_old_input' => [
                    'order_id' => $other->order_id, 'offer_id' => $other->offer_id,
                    'customer_id' => $other->customer_id, 'craftman_id' => $other->craftman_id,
                    'status' => 'in_progress', 'started_at' => '2027-05-06T10:20', 'completed_at' => '2027-05-06T09:20',
                ],
                'errors' => $errors,
            ])->get(route('service-jobs.edit', $job))->assertOk()->getContent();

        foreach ([$other->order_id, $other->offer_id, $other->customer_id, $other->craftman_id] as $id) {
            $this->assertStringContainsString('value="'.$id.'" selected', $content);
        }
        foreach (['value="in_progress" selected', '2027-05-06T10:20', '2027-05-06T09:20', 'وقت البدء مطلوب.', 'وقت الإنجاز لا يمكن أن يسبق وقت البدء.'] as $value) {
            $this->assertStringContainsString($value, $content);
        }
        $this->assertStringContainsString('cms-form-errors-summary', $content);
        $this->assertGreaterThanOrEqual(2, substr_count($content, 'is-invalid'));
    }

    private function adminWith(string $permission): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'service-job-form-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));

        return $admin;
    }
}
