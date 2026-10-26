<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsResourceFilterSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_migrated_indexes_accept_validated_filters(): void
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'filter-admin', 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        foreach (['orders.view', 'offers.view', 'service-jobs.view', 'craftmen.view', 'customers.view', 'reviews.view', 'verifications.view'] as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
        }

        $this->actingAs($admin, 'admin');
        $this->get(route('orders.index', ['search' => 'x', 'sort' => 'budget_desc']))->assertOk();
        $this->get(route('offers.index', ['search' => 'x', 'status' => 'pending', 'price_min' => 10, 'price_max' => 20, 'date_from' => '2026-01-01', 'date_to' => '2026-12-31', 'sort' => 'price_desc']))->assertOk();
        $this->get(route('service-jobs.index', ['search' => 'x', 'status' => 'pending', 'date_from' => '2026-01-01', 'date_to' => '2026-12-31', 'started_from' => '2026-01-01', 'started_to' => '2026-12-31', 'completed_from' => '2026-01-01', 'completed_to' => '2026-12-31', 'sort' => 'completed_desc']))->assertOk();
        $this->get(route('craftmen.index', ['search' => 'x', 'sort' => 'name_asc', 'verification_status' => 'unverified']))->assertOk();
        $this->get(route('customers.index', ['search' => 'x', 'sort' => 'oldest']))->assertOk();
        $this->get(route('reviews.index', ['rating' => 5, 'sort' => 'rating_desc']))->assertOk();
        $this->get(route('verifications.index', ['status' => 'pending', 'sort' => 'pending_first']))->assertOk();
    }
}
