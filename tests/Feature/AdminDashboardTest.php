<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\ServiceJob;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_access_is_isolated_to_authorized_admins(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer')->get(route('dashboard'))->assertRedirect(route('login'));
        auth('customer')->logout();

        $craftman = Craftman::factory()->create();
        $this->actingAs($craftman->user, 'craftman')->get(route('dashboard'))->assertRedirect(route('login'));
        auth('craftman')->logout();

        $this->actingAs($this->admin(), 'admin')->get(route('dashboard'))->assertForbidden();
        auth('admin')->logout();

        $this->actingAs($this->adminWith(['dashboard.view']), 'admin')
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('cms.dashboard')
            ->assertSee('data-testid="admin-dashboard"', false);
    }

    public function test_main_metrics_use_real_scoped_database_counts(): void
    {
        Customer::factory()->count(2)->create();
        $inactiveCustomer = Customer::factory()->create();
        $inactiveCustomer->user->update(['status' => 'inactive']);
        $blockedCustomer = Customer::factory()->create();
        $blockedCustomer->user->update(['status' => 'blocked']);
        Customer::factory()->create()->delete();
        $deletedCustomerUser = Customer::factory()->create();
        $deletedCustomerUser->user->delete();

        Craftman::factory()->count(3)->create();
        $inactiveCraftman = Craftman::factory()->create();
        $inactiveCraftman->user->update(['status' => 'inactive']);
        $blockedCraftman = Craftman::factory()->create();
        $blockedCraftman->user->update(['status' => 'blocked']);
        Craftman::factory()->create()->delete();

        Order::factory()->count(2)->create(['status' => 'pending']);
        Order::factory()->create(['status' => 'completed']);
        Order::factory()->create(['status' => 'pending'])->delete();

        Offer::factory()->count(2)->create(['status' => 'pending']);
        Offer::factory()->create(['status' => 'accepted']);
        Offer::factory()->create(['status' => 'pending'])->delete();

        ServiceJob::factory()->create(['status' => 'pending']);
        ServiceJob::factory()->create(['status' => 'in_progress']);
        ServiceJob::factory()->create(['status' => 'completed']);
        ServiceJob::factory()->create(['status' => 'pending'])->delete();

        Review::factory()->count(2)->create(['status' => 'pending']);
        Review::factory()->create(['status' => 'approved']);
        Review::factory()->create(['status' => 'pending'])->delete();

        Verification::factory()->count(2)->create(['status' => 'pending']);
        Verification::factory()->create(['status' => 'approved']);
        Verification::factory()->create(['status' => 'pending'])->delete();

        $portfolioCraftman = Craftman::factory()->create();
        Portfolio::factory()->count(2)->create(['craftman_id' => $portfolioCraftman->id, 'status' => true]);
        Portfolio::factory()->create(['craftman_id' => $portfolioCraftman->id, 'status' => false]);
        Portfolio::factory()->create(['craftman_id' => $portfolioCraftman->id, 'status' => true])->delete();

        $response = $this->actingAs($this->adminWith(['dashboard.view']), 'admin')->get(route('dashboard'));

        $response->assertOk()->assertViewHas('metrics', function (array $metrics): bool {
            return $metrics === [
                'active_customers' => Customer::query()->whereHas('user', fn ($query) => $query->where('status', 'active'))->count(),
                'active_craftmen' => Craftman::query()->whereHas('user', fn ($query) => $query->where('status', 'active'))->count(),
                'pending_orders' => Order::query()->where('status', 'pending')->count(),
                'pending_offers' => Offer::query()->where('status', 'pending')->count(),
                'active_service_jobs' => ServiceJob::query()->whereIn('status', ['pending', 'in_progress'])->count(),
                'pending_reviews' => Review::query()->where('status', 'pending')->count(),
                'pending_verifications' => Verification::query()->where('status', 'pending')->count(),
                'active_portfolios' => Portfolio::query()->where('status', true)->count(),
            ]
                && $metrics['active_customers'] < Customer::query()->count()
                && $metrics['active_craftmen'] < Craftman::query()->count()
                && $metrics['pending_orders'] < Order::query()->count()
                && $metrics['pending_offers'] < Offer::query()->count()
                && $metrics['active_service_jobs'] < ServiceJob::query()->count();
        });
    }

    public function test_status_summaries_include_every_real_status_and_exclude_deleted_records(): void
    {
        foreach (['pending', 'accepted', 'in_progress', 'completed', 'cancelled'] as $status) {
            Order::factory()->create(['status' => $status]);
        }
        Order::factory()->create(['status' => 'pending'])->delete();

        foreach (['pending', 'in_progress', 'completed', 'cancelled'] as $status) {
            ServiceJob::factory()->create(['status' => $status]);
        }
        ServiceJob::factory()->create(['status' => 'pending'])->delete();

        foreach (['pending', 'approved', 'rejected'] as $status) {
            Review::factory()->create(['status' => $status]);
            Verification::factory()->create(['status' => $status]);
        }
        Review::factory()->create(['status' => 'pending'])->delete();
        Verification::factory()->create(['status' => 'pending'])->delete();

        $response = $this->actingAs($this->adminWith(['dashboard.view']), 'admin')->get(route('dashboard'));

        $response->assertViewHas('statusSummaries', [
            'orders' => $this->modelStatusCounts(Order::class, ['pending', 'accepted', 'in_progress', 'completed', 'cancelled']),
            'service_jobs' => $this->modelStatusCounts(ServiceJob::class, ['pending', 'in_progress', 'completed', 'cancelled']),
            'reviews' => $this->modelStatusCounts(Review::class, ['pending', 'approved', 'rejected']),
            'verifications' => $this->modelStatusCounts(Verification::class, ['pending', 'approved', 'rejected']),
        ]);
    }

    public function test_pending_moderation_lists_are_pending_only_latest_first_and_limited_to_five(): void
    {
        $verificationIds = [];
        $reviewIds = [];

        for ($index = 0; $index < 7; $index++) {
            $createdAt = Carbon::parse('2026-01-01')->addDays($index);
            $verificationIds[] = Verification::factory()->create([
                'status' => 'pending',
                'created_at' => $createdAt,
            ])->id;
            $reviewIds[] = Review::factory()->create([
                'status' => 'pending',
                'created_at' => $createdAt,
            ])->id;
        }

        Verification::factory()->create(['status' => 'approved', 'created_at' => now()->addYear()]);
        Review::factory()->create(['status' => 'approved', 'created_at' => now()->addYear()]);

        $response = $this->actingAs($this->adminWith([
            'dashboard.view', 'verifications.view', 'reviews.view',
        ]), 'admin')->get(route('dashboard'));

        $response->assertViewHas('pendingVerifications', function ($items) use ($verificationIds): bool {
            return $items->count() === 5
                && $items->pluck('id')->all() === array_reverse(array_slice($verificationIds, -5))
                && $items->every(fn ($item) => $item->status === 'pending');
        });
        $response->assertViewHas('pendingReviews', function ($items) use ($reviewIds): bool {
            return $items->count() === 5
                && $items->pluck('id')->all() === array_reverse(array_slice($reviewIds, -5))
                && $items->every(fn ($item) => $item->status === 'pending');
        });
    }

    public function test_recent_orders_and_jobs_are_newest_first_limited_and_exclude_deleted(): void
    {
        $orderIds = [];
        $jobIds = [];

        for ($index = 0; $index < 7; $index++) {
            $time = Carbon::parse('2030-02-01')->addDays($index);
            $orderIds[] = Order::factory()->create(['created_at' => $time])->id;
            $jobIds[] = ServiceJob::factory()->create(['updated_at' => $time])->id;
        }

        Order::factory()->create(['created_at' => Carbon::parse('2031-01-01')])->delete();
        ServiceJob::factory()->create(['updated_at' => Carbon::parse('2031-01-01')])->delete();

        $response = $this->actingAs($this->adminWith(['dashboard.view']), 'admin')->get(route('dashboard'));

        $response->assertViewHas('recentOrders', fn ($items): bool => $items->pluck('id')->all() === array_reverse(array_slice($orderIds, -5)));
        $response->assertViewHas('recentServiceJobs', fn ($items): bool => $items->pluck('id')->all() === array_reverse(array_slice($jobIds, -5)));
    }

    public function test_moderation_links_and_records_are_not_rendered_without_resource_permissions(): void
    {
        Review::factory()->create(['status' => 'pending']);
        Verification::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->adminWith(['dashboard.view']), 'admin')
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('data-testid="pending-reviews-list"', false)
            ->assertDontSee('data-testid="pending-verifications-list"', false)
            ->assertDontSee('href="'.route('reviews.index').'"', false)
            ->assertDontSee('href="'.route('verifications.index').'"', false);

        $response->assertViewHas('pendingReviews', fn ($items): bool => $items->isEmpty());
        $response->assertViewHas('pendingVerifications', fn ($items): bool => $items->isEmpty());
    }

    public function test_dashboard_is_a_child_view_and_has_no_local_storage_prototype(): void
    {
        $this->actingAs($this->adminWith(['dashboard.view']), 'admin')
            ->get('/cms/admin')
            ->assertOk()
            ->assertViewIs('cms.dashboard')
            ->assertDontSee('localStorage', false)
            ->assertDontSee('requestsChart', false)
            ->assertDontSee('craftsmenChart', false);
    }

    private function adminWith(array $permissions): User
    {
        $admin = $this->admin();
        $role = $admin->roles()->firstOrFail();

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'admin']);
            $role->givePermissionTo($permission);
        }

        return $admin;
    }

    private function modelStatusCounts(string $model, array $statuses): array
    {
        return collect($statuses)
            ->mapWithKeys(fn (string $status) => [
                $status => $model::query()->where('status', $status)->count(),
            ])
            ->all();
    }

    private function admin(): User
    {
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $role = Role::create(['name' => 'dashboard-admin-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        return $admin;
    }
}
