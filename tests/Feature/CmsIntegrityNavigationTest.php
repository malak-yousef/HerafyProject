<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsIntegrityNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_consistent_pending_service_job_can_be_created(): void
    {
        [$order, $offer] = $this->acceptedWorkflow();

        $this->actingAs($this->superAdmin(), 'admin')
            ->postJson(route('service-jobs.store'), $this->serviceJobPayload($order, $offer))
            ->assertRedirect(route('service-jobs.index'));

        $this->assertDatabaseHas('service_jobs', [
            'order_id' => $order->id,
            'offer_id' => $offer->id,
            'customer_id' => $order->customer_id,
            'craftman_id' => $offer->craftman_id,
            'status' => 'pending',
        ]);
    }

    public function test_service_job_rejects_mismatched_order_offer_craftman_and_customer(): void
    {
        [$order, $offer] = $this->acceptedWorkflow();
        [$otherOrder, $otherOffer] = $this->acceptedWorkflow();
        $this->actingAs($this->superAdmin(), 'admin');

        $this->postJson(route('service-jobs.store'), $this->serviceJobPayload($order, $otherOffer))
            ->assertUnprocessable()->assertJsonValidationErrors('offer_id');

        $payload = $this->serviceJobPayload($order, $offer);
        $payload['craftman_id'] = $otherOffer->craftman_id;
        $this->postJson(route('service-jobs.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('craftman_id');

        $payload = $this->serviceJobPayload($order, $offer);
        $payload['customer_id'] = $otherOrder->customer_id;
        $this->postJson(route('service-jobs.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('customer_id');
    }

    public function test_service_job_rejects_invalid_timestamps_statuses_and_duplicates(): void
    {
        [$order, $offer] = $this->acceptedWorkflow();
        $this->actingAs($this->superAdmin(), 'admin');

        $payload = $this->serviceJobPayload($order, $offer);
        $payload['started_at'] = now()->toDateTimeString();
        $this->postJson(route('service-jobs.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('started_at');

        $payload = $this->serviceJobPayload($order, $offer);
        $payload['status'] = 'in_progress';
        $payload['started_at'] = now()->toDateTimeString();
        $this->postJson(route('service-jobs.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('status');

        ServiceJob::create($this->serviceJobPayload($order, $offer));
        $this->postJson(route('service-jobs.store'), $this->serviceJobPayload($order, $offer))
            ->assertUnprocessable()->assertJsonValidationErrors(['order_id', 'offer_id']);
    }

    public function test_offer_requires_matching_profession_compatible_state_and_no_duplicate(): void
    {
        $profession = Profession::factory()->create();
        $order = Order::factory()->create(['profession_id' => $profession->id, 'status' => 'pending']);
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $this->actingAs($this->superAdmin(), 'admin');

        $payload = $this->offerPayload($order, $craftman);
        $this->postJson(route('offers.store'), $payload)->assertRedirect(route('offers.index'));

        $this->postJson(route('offers.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('craftman_id');

        $otherCraftman = Craftman::factory()->create();
        $this->postJson(route('offers.store'), $this->offerPayload($order, $otherCraftman))
            ->assertUnprocessable()->assertJsonValidationErrors('craftman_id');

        $closedOrder = Order::factory()->create(['profession_id' => $profession->id, 'status' => 'accepted']);
        $this->postJson(route('offers.store'), $this->offerPayload($closedOrder, $craftman))
            ->assertUnprocessable()->assertJsonValidationErrors('status');
    }

    public function test_review_requires_the_completed_matching_workflow_and_remains_unique(): void
    {
        [$order, $offer] = $this->completedWorkflow();
        $job = ServiceJob::create([
            ...$this->serviceJobPayload($order, $offer),
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);
        $this->actingAs($this->superAdmin(), 'admin');

        $payload = $this->reviewPayload($order, $job);
        $this->postJson(route('reviews.store'), $payload)->assertRedirect(route('reviews.index'));

        $this->postJson(route('reviews.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('request_id');
    }

    public function test_review_rejects_wrong_customer_wrong_craftman_and_order_without_completed_job(): void
    {
        [$order, $offer] = $this->completedWorkflow();
        $job = ServiceJob::create([
            ...$this->serviceJobPayload($order, $offer),
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);
        $this->actingAs($this->superAdmin(), 'admin');

        $payload = $this->reviewPayload($order, $job);
        $payload['customer_id'] = Customer::factory()->create()->id;
        $this->postJson(route('reviews.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('customer_id');

        $payload = $this->reviewPayload($order, $job);
        $payload['craftsman_id'] = Craftman::factory()->create()->id;
        $this->postJson(route('reviews.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('craftsman_id');

        $pendingOrder = Order::factory()->create();
        $payload = $this->reviewPayload($pendingOrder, $job);
        $this->postJson(route('reviews.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('request_id');
    }

    public function test_order_rejects_area_from_another_city(): void
    {
        $city = City::factory()->create();
        $area = Area::factory()->create(['city_id' => City::factory()->create()->id]);
        $payload = Order::factory()->make([
            'city_id' => $city->id,
            'area_id' => $area->id,
        ])->toArray();

        $this->actingAs($this->superAdmin(), 'admin')
            ->postJson(route('orders.store'), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('area_id');
    }

    public function test_dead_role_permission_routes_are_absent_and_dashboard_is_canonical(): void
    {
        foreach (['roles.show', 'roles.edit', 'roles.update', 'permissions.show', 'permissions.edit', 'permissions.update'] as $name) {
            $this->assertNull(Route::getRoutes()->getByName($name));
        }

        $admin = $this->adminWith('dashboard.view');
        $this->actingAs($admin, 'admin')->get('/cms/admin')->assertOk();
        $this->get('/cms/admin/temp')->assertRedirect('/cms/admin');
    }

    public function test_sidebar_aligns_child_links_and_removes_dead_navigation(): void
    {
        $admin = $this->adminWith('categories.view');

        $response = $this->actingAs($admin, 'admin')->get(route('categories.index'))->assertOk();
        $response->assertSee('href="'.route('categories.index').'"', false)
            ->assertDontSee('href="'.route('categories.create').'"', false)
            ->assertSee('href="'.route('categories_trashed').'"', false)
            ->assertDontSee('quickEdit()', false);

        $this->assertSame(1, substr_count($response->getContent(), 'action="'.route('logout').'"'));
    }

    private function acceptedWorkflow(): array
    {
        $profession = Profession::factory()->create();
        $order = Order::factory()->create(['profession_id' => $profession->id, 'status' => 'accepted']);
        $craftman = Craftman::factory()->create(['profession_id' => $profession->id]);
        $offer = Offer::factory()->create([
            'order_id' => $order->id,
            'craftman_id' => $craftman->id,
            'status' => 'accepted',
        ]);

        return [$order, $offer];
    }

    private function completedWorkflow(): array
    {
        [$order, $offer] = $this->acceptedWorkflow();
        $order->update(['status' => 'completed']);
        $offer->update(['status' => 'completed']);

        return [$order->fresh(), $offer->fresh()];
    }

    private function serviceJobPayload(Order $order, Offer $offer): array
    {
        return [
            'order_id' => $order->id,
            'offer_id' => $offer->id,
            'customer_id' => $order->customer_id,
            'craftman_id' => $offer->craftman_id,
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    private function offerPayload(Order $order, Craftman $craftman): array
    {
        return [
            'order_id' => $order->id,
            'craftman_id' => $craftman->id,
            'price' => 250,
            'message' => 'عرض متوافق',
            'status' => 'pending',
        ];
    }

    private function reviewPayload(Order $order, ServiceJob $job): array
    {
        return [
            'rating' => 5,
            'comment' => 'عمل ممتاز',
            'craftsman_id' => $job->craftman_id,
            'customer_id' => $order->customer_id,
            'request_id' => $order->id,
            'status' => 'pending',
        ];
    }

    private function superAdmin(): User
    {
        return $this->admin('super-admin');
    }

    private function adminWith(string $permissionName): User
    {
        $admin = $this->admin('admin-'.str_replace('.', '-', $permissionName));
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'admin']);
        $admin->roles()->firstOrFail()->givePermissionTo($permission);

        return $admin;
    }

    private function admin(string $roleName): User
    {
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        return $admin;
    }
}
