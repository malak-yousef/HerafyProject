<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsOfferFormUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_renders_shared_validation_semantics_and_safe_order_preselection(): void
    {
        $offerData = Offer::factory()->make();
        $order = Order::findOrFail($offerData->order_id);
        $craftman = $offerData->craftman;
        $order->update(['title' => 'طلب الصيانة المقروء']);
        $craftman->user->update(['name' => 'الحرفي المقروء']);

        $content = $this->actingAs($this->adminWith('offers.create'), 'admin')
            ->get(route('offers.create', ['order_id' => $order->id]))
            ->assertOk()->getContent();

        foreach (['order_id', 'craftman_id', 'price', 'message', 'status'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $content);
        }
        $this->assertStringContainsString('طلب الصيانة المقروء', $content);
        $this->assertStringContainsString('الحرفي المقروء', $content);
        $this->assertStringContainsString('value="'.$order->id.'" data-profession-id="'.$order->profession_id.'" selected', $content);
        $this->assertStringContainsString('value="'.$craftman->id.'" data-profession-id="'.$craftman->profession_id.'"', $content);
        $this->assertSame(1, substr_count($content, 'cms/js/marketplace-relations.js'));
        $this->assertSame(1, substr_count($content, "CmsMarketplaceRelations.initOffer('#offer-order', '#offer-craftman')"));
        $this->assertStringContainsString('method="POST"', $content);
        $this->assertStringContainsString('name="_token"', $content);
        foreach (['قيد الانتظار', 'مقبول', 'قيد التنفيذ', 'مكتمل', 'ملغي'] as $label) {
            $this->assertStringContainsString($label, $content);
        }
    }

    public function test_invalid_or_deleted_query_order_is_not_preselected(): void
    {
        $available = Order::factory()->create();
        $deleted = Order::factory()->create();
        $deleted->delete();
        $admin = $this->adminWith('offers.create');

        foreach ([$deleted->id, 999999] as $queryId) {
            $content = $this->actingAs($admin, 'admin')
                ->get(route('offers.create', ['order_id' => $queryId]))
                ->assertOk()->getContent();
            $this->assertStringContainsString('value="'.$available->id.'"', $content);
            $this->assertStringNotContainsString('value="'.$queryId.'" selected', $content);
        }
    }

    public function test_edit_and_validation_failure_preserve_values_and_render_field_errors(): void
    {
        $offer = Offer::factory()->create(['price' => 325, 'message' => 'رسالة أصلية']);
        $other = Offer::factory()->create();
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag(['craftman_id' => ['مهنة الحرفي لا تطابق مهنة الطلب.']]));

        $content = $this->actingAs($this->adminWith('offers.update'), 'admin')
            ->withSession([
                '_old_input' => ['order_id' => $other->order_id, 'craftman_id' => $other->craftman_id, 'price' => '444', 'message' => 'رسالة قديمة', 'status' => 'cancelled'],
                'errors' => $errors,
            ])->get(route('offers.edit', $offer))->assertOk()->getContent();

        $this->assertStringContainsString('value="'.$other->order_id.'" data-profession-id="'.$other->order->profession_id.'" selected', $content);
        $this->assertStringContainsString('value="'.$other->craftman_id.'" data-profession-id="'.$other->craftman->profession_id.'" selected', $content);
        foreach (['444', 'رسالة قديمة', 'value="cancelled" selected'] as $value) {
            $this->assertStringContainsString($value, $content);
        }
        $this->assertStringContainsString('cms-form-errors-summary', $content);
        $this->assertStringContainsString('مهنة الحرفي لا تطابق مهنة الطلب.', $content);
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertStringContainsString('href="'.route('offers.index').'"', $content);
        $this->assertSame(1, substr_count($content, 'cms/js/marketplace-relations.js'));
    }

    private function adminWith(string $permission): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'offer-form-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));

        return $admin;
    }
}
