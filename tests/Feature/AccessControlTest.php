<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_the_cms(): void
    {
        $this->assertAllGuardsAreGuests();

        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $this->assignAdminRole($admin);

        $this->actingAs($admin, 'admin')
            ->get('/cms/admin')
            ->assertOk();
    }

    public function test_customer_cannot_access_the_cms(): void
    {
        $this->assertAllGuardsAreGuests();

        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->get('/cms/admin')
            ->assertRedirect(route('login'));
    }

    public function test_craftman_cannot_access_the_cms(): void
    {
        $this->assertAllGuardsAreGuests();

        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->get('/cms/admin')
            ->assertRedirect(route('login'));
    }

    public function test_user_authenticated_only_with_the_web_guard_cannot_access_the_cms(): void
    {
        $this->assertAllGuardsAreGuests();

        $user = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);

        $this->actingAs($user, 'web')
            ->get('/cms/admin')
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_the_cms(): void
    {
        $this->assertAllGuardsAreGuests();

        $this->get('/cms/admin')
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_protected_offer_management_routes(): void
    {
        $offer = Offer::factory()->create();

        $this->get(route('herafy.offer.index'))
            ->assertRedirect(route('login'));
        $this->get(route('herafy.offer.show', $offer))
            ->assertRedirect(route('login'));
        $this->delete("/herafy/offers/{$offer->id}")
            ->assertMethodNotAllowed();
    }

    public function test_craftman_can_access_their_own_offer(): void
    {
        $craftman = Craftman::factory()->create();
        $offer = Offer::factory()->create([
            'craftman_id' => $craftman->id,
        ]);

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.offer.show', $offer))
            ->assertOk()
            ->assertViewHas('offer', fn (Offer $viewOffer) => $viewOffer->is($offer));
    }

    public function test_craftman_cannot_access_another_craftmans_offer(): void
    {
        $craftman = Craftman::factory()->create();
        $otherOffer = Offer::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.offer.show', $otherOffer))
            ->assertForbidden();
    }

    public function test_successful_login_authenticates_the_correct_guard_and_regenerates_the_session(): void
    {
        $password = 'secure-password';
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
            'password' => Hash::make($password),
        ]);
        $this->assignAdminRole($admin);
        $oldSessionId = session()->getId();

        $this->postJson('/login', [
            'email' => $admin->email,
            'password' => $password,
        ])->assertOk()
            ->assertJsonPath('redirect', url('/cms/admin'));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNotSame($oldSessionId, session()->getId());
    }

    private function assignAdminRole(User $admin): void
    {
        $role = Role::firstOrCreate([
            'name' => 'test-admin',
            'guard_name' => 'admin',
        ]);
        $admin->roles()->attach($role);
        $permission = Permission::firstOrCreate([
            'name' => 'dashboard.view',
            'guard_name' => 'admin',
        ]);
        $role->givePermissionTo($permission);
    }

    private function assertAllGuardsAreGuests(): void
    {
        foreach (['web', 'admin', 'customer', 'craftman'] as $guard) {
            $this->assertGuest($guard);
        }
    }
}
