<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsOfferIndexUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_offer_statuses_and_readable_relationships_render_through_shared_ui(): void
    {
        $order = Order::factory()->create(['title' => 'طلب واجهة العروض']);
        $craftman = Craftman::factory()->create();
        $craftman->user->update(['name' => 'حرفي واجهة العروض']);

        foreach (['pending', 'accepted', 'in_progress', 'completed', 'cancelled'] as $status) {
            Offer::factory()->create(['order_id' => $order->id, 'craftman_id' => $craftman->id, 'status' => $status]);
        }

        $response = $this->actingAs($this->adminWith(['offers.view']), 'admin')->get(route('offers.index'))->assertOk();

        foreach (['قيد الانتظار', 'مقبول', 'قيد التنفيذ', 'مكتمل', 'ملغي'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('طلب واجهة العروض')
            ->assertSee('حرفي واجهة العروض')
            ->assertSee('class="badge bg-warning text-dark"', false)
            ->assertSee('class="badge bg-primary"', false)
            ->assertSee('class="badge bg-success"', false)
            ->assertSee('class="badge bg-danger"', false);
    }

    public function test_offer_empty_states_and_filter_values_are_preserved(): void
    {
        $this->actingAs($this->adminWith(['offers.view']), 'admin');

        $this->get(route('offers.index'))->assertOk()->assertSee('لا توجد عروض حتى الآن.');

        $this->get(route('offers.index', ['search' => 'غير-موجود', 'status' => 'pending', 'sort' => 'price_desc']))
            ->assertOk()
            ->assertSee('لا توجد نتائج مطابقة لخيارات البحث.')
            ->assertSee('إعادة تعيين الفلاتر')
            ->assertSee('value="pending" selected', false)
            ->assertSee('value="price_desc" selected', false)
            ->assertSee('href="'.route('offers.index').'"', false);
    }

    public function test_offer_actions_are_permission_aware_and_delete_is_a_mutation_form(): void
    {
        $offer = Offer::factory()->create();
        $content = $this->actingAs($this->adminWith(['offers.view', 'offers.update', 'offers.delete']), 'admin')
            ->get(route('offers.index'))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('offers.show', $offer).'"', $content);
        $this->assertStringContainsString('href="'.route('offers.edit', $offer).'"', $content);
        $this->assertStringContainsString('action="'.route('offers.destroy', $offer).'" method="POST"', $content);
        $this->assertStringContainsString('name="_method" value="DELETE"', $content);
        $this->assertSame(1, substr_count($content, 'href="'.route('offers.destroy', $offer).'"'));

        auth('admin')->logout();
        $readOnly = $this->actingAs($this->adminWith(['offers.view']), 'admin')
            ->get(route('offers.index'))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('offers.show', $offer).'"', $readOnly);
        $this->assertStringNotContainsString('href="'.route('offers.edit', $offer).'"', $readOnly);
        $this->assertStringNotContainsString('action="'.route('offers.destroy', $offer).'"', $readOnly);
    }

    public function test_orders_add_offer_cta_is_authorized_and_cannot_bypass_offer_integrity_validation(): void
    {
        $order = Order::factory()->create();
        $craftman = Craftman::factory()->create();
        $admin = $this->adminWith(['orders.view', 'offers.create']);

        $ordersContent = $this->actingAs($admin, 'admin')->get(route('orders.index'))->assertOk()->getContent();
        $ctaUrl = route('offers.create', ['order_id' => $order->id]);
        $this->assertStringContainsString('href="'.$ctaUrl.'"', $ordersContent);
        $this->get($ctaUrl)->assertOk()->assertSee('name="craftman_id"', false);

        $this->post(route('offers.store'), [
            'order_id' => $order->id,
            'craftman_id' => $craftman->id,
            'price' => 100,
            'message' => 'عرض غير متوافق',
            'status' => $order->status,
        ])->assertSessionHasErrors('craftman_id');

        $this->assertDatabaseCount('offers', 0);
    }

    private function adminWith(array $permissions): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'offers-ui-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach ($permissions as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        return $admin;
    }
}
