<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsReviewFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_renders_required_relationships_rating_and_post_actions(): void
    {
        $content = $this->actingAs($this->adminWith(['reviews.create']), 'admin')
            ->get(route('reviews.create'))->assertOk()->getContent();

        foreach (['craftsman_id', 'customer_id', 'request_id', 'rating', 'comment'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $content);
        }

        $this->assertSame(4, substr_count($content, 'class="cms-required"'));
        $this->assertStringContainsString('action="'.route('reviews.store').'" method="POST"', $content);
        $this->assertStringContainsString('type="submit"', $content);
        $this->assertStringContainsString('href="'.route('reviews.index').'"', $content);
        $this->assertStringContainsString('التعليق <small class="text-muted">(اختياري)</small>', $content);
    }

    public function test_create_validation_summary_field_error_and_old_input_render_after_failure(): void
    {
        $craftman = Craftman::factory()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()->create();
        $this->actingAs($this->adminWith(['reviews.create']), 'admin');

        $this->from(route('reviews.create'))->post(route('reviews.store'), [
            'craftsman_id' => $craftman->id,
            'customer_id' => $customer->id,
            'request_id' => $order->id,
            'rating' => 8,
            'comment' => 'تعليق محفوظ بعد الخطأ',
        ])->assertRedirect(route('reviews.create'))->assertSessionHasErrors('rating');

        $response = $this->get(route('reviews.create'))->assertOk();
        $response->assertSee('يرجى مراجعة الحقول التالية وتصحيح الأخطاء.')
            ->assertSee('تعليق محفوظ بعد الخطأ')
            ->assertSee('name="rating" class="form-select is-invalid"', false)
            ->assertSee('invalid-feedback', false);
    }

    public function test_integrity_error_is_visible_beside_the_relevant_relationship(): void
    {
        $craftman = Craftman::factory()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()->create();
        $this->actingAs($this->adminWith(['reviews.create']), 'admin');

        $this->from(route('reviews.create'))->post(route('reviews.store'), [
            'craftsman_id' => $craftman->id,
            'customer_id' => $customer->id,
            'request_id' => $order->id,
            'rating' => 4,
            'comment' => 'اختبار التكامل',
        ])->assertRedirect(route('reviews.create'))->assertSessionHasErrors('customer_id');

        $this->get(route('reviews.create'))->assertOk()
            ->assertSee('يجب أن يكون العميل هو صاحب الطلب المحدد.')
            ->assertSee('name="customer_id" class="form-select is-invalid"', false);
    }

    public function test_edit_form_preselects_model_and_old_values_with_arabic_statuses(): void
    {
        $review = Review::factory()->create(['rating' => 3, 'status' => 'approved', 'comment' => 'تعليق أصلي']);
        $content = $this->actingAs($this->adminWith(['reviews.update']), 'admin')
            ->get(route('reviews.edit', $review))->assertOk()->getContent();

        $this->assertStringContainsString('value="'.$review->craftsman_id.'" selected', $content);
        $this->assertStringContainsString('value="'.$review->customer_id.'" selected', $content);
        $this->assertStringContainsString('value="'.$review->request_id.'" selected', $content);
        $this->assertStringContainsString('value="3" selected', $content);
        $this->assertStringContainsString('value="approved" selected', $content);
        foreach (['ظاهر', 'مخفي'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
        $this->assertStringContainsString('name="rating" class="form-select" required disabled', $content);
        $this->assertStringContainsString('name="comment" rows="5" class="form-control"', $content);
        $this->assertStringContainsString('readonly', $content);
        $this->assertStringContainsString('حفظ حالة الظهور', $content);
        $this->assertStringContainsString('action="'.route('reviews.update', $review).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
    }

    public function test_admin_can_hide_review_but_cannot_change_customer_rating_or_comment(): void
    {
        $review = Review::factory()->create(['rating' => 5, 'status' => 'approved', 'comment' => 'رأي العميل الأصلي']);

        $this->actingAs($this->adminWith(['reviews.update']), 'admin')
            ->put(route('reviews.update', $review), [
                'status' => 'rejected',
                'rating' => 1,
                'comment' => 'تعليق معدل',
                'customer_id' => 999999,
                'craftsman_id' => 999999,
                'request_id' => 999999,
            ])
            ->assertRedirect(route('reviews.index'))
            ->assertSessionHas('success');

        $review->refresh();
        $this->assertSame('rejected', $review->status);
        $this->assertSame(5, $review->rating);
        $this->assertSame('رأي العميل الأصلي', $review->comment);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'review-form-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
