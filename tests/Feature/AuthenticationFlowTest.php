<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_admin_can_login_on_the_admin_guard(): void
    {
        $admin = $this->activeAdmin();

        $this->login($admin)
            ->assertOk()
            ->assertJsonPath('redirect', url('/cms/admin'));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertGuest('customer');
        $this->assertGuest('craftman');
    }

    public function test_seeded_admin_has_explicit_identity_and_can_login(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin1@example.com')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->postJson('/login', [
            'email' => $admin->email,
            'password' => '123123',
        ])->assertOk()->assertJsonPath('redirect', url('/cms/admin'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_controller_creates_an_explicit_admin_identity(): void
    {
        $creator = $this->activeAdmin();
        $permission = Permission::create(['name' => 'admins.create', 'guard_name' => 'admin']);
        $creator->roles()->firstOrFail()->givePermissionTo($permission);
        $this->actingAs($creator, 'admin');

        $this->postJson(route('admins.store'), [
            'name' => 'Created Admin',
            'user_name' => 'created_admin',
            'email' => 'created-admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'mobile' => '0599999999',
            'gender' => 'male',
            'address' => 'Gaza',
            'date' => now()->toDateString(),
            'status' => 'active',
            'role_name' => 'test-admin',
        ])->assertOk();

        $admin = User::where('email', 'created-admin@example.com')->firstOrFail();
        $this->assertTrue($admin->isAdmin());

        auth('admin')->logout();
        $this->login($admin)->assertOk();
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_active_customer_can_login_on_the_customer_guard(): void
    {
        $customer = Customer::factory()->create();
        $user = $this->activate($customer->user);

        $this->login($user)
            ->assertOk()
            ->assertJsonPath('redirect', url('/herafy/main'));

        $this->assertAuthenticatedAs($user, 'customer');
        $this->assertGuest('admin');
        $this->assertGuest('craftman');
    }

    public function test_active_craftman_can_login_on_the_craftman_guard(): void
    {
        $craftman = Craftman::factory()->create();
        $user = $this->activate($craftman->user);

        $this->login($user)
            ->assertOk()
            ->assertJsonPath('redirect', route('herafy.craftman.dashboard'));

        $this->assertAuthenticatedAs($user, 'craftman');
        $this->assertGuest('admin');
        $this->assertGuest('customer');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = $this->activeUser(['status' => 'inactive']);

        $this->login($user)->assertForbidden();

        $this->assertAllGuardsAreGuests();
    }

    public function test_wrong_password_cannot_login(): void
    {
        $user = $this->activeUser();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'remember' => false,
        ])->assertStatus(400);

        $this->assertAllGuardsAreGuests();
    }

    public function test_login_without_remember_does_not_create_a_recaller(): void
    {
        $admin = $this->activeAdmin();

        $this->login($admin, false)
            ->assertOk()
            ->assertCookieMissing(auth('admin')->getRecallerName());

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNull($admin->fresh()->remember_token);
    }

    public function test_login_with_remember_creates_an_admin_recaller(): void
    {
        $admin = $this->activeAdmin();

        $this->login($admin, true)
            ->assertOk()
            ->assertCookie(auth('admin')->getRecallerName());

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNotNull($admin->fresh()->remember_token);
    }

    public function test_remember_login_works_for_customer_and_craftman_guards(): void
    {
        $customer = Customer::factory()->create();
        $customerUser = $this->activate($customer->user);

        $this->login($customerUser, true)
            ->assertOk()
            ->assertCookie(auth('customer')->getRecallerName());
        $this->assertAuthenticatedAs($customerUser, 'customer');
        $this->assertNotNull($customerUser->fresh()->remember_token);

        $this->post('/logout')->assertRedirect(route('login'));

        $craftman = Craftman::factory()->create();
        $craftmanUser = $this->activate($craftman->user);

        $this->login($craftmanUser, true)
            ->assertOk()
            ->assertCookie(auth('craftman')->getRecallerName());
        $this->assertAuthenticatedAs($craftmanUser, 'craftman');
        $this->assertNotNull($craftmanUser->fresh()->remember_token);
    }

    public function test_successful_login_regenerates_the_session_id(): void
    {
        $admin = $this->activeAdmin();
        $oldSessionId = session()->getId();

        $this->login($admin)->assertOk();

        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_logout_clears_the_admin_guard(): void
    {
        $this->assertLogoutClearsGuard('admin', $this->activeAdmin());
    }

    public function test_logout_clears_the_customer_guard(): void
    {
        $customer = Customer::factory()->create();

        $this->assertLogoutClearsGuard('customer', $this->activate($customer->user));
    }

    public function test_logout_clears_the_craftman_guard(): void
    {
        $craftman = Craftman::factory()->create();

        $this->assertLogoutClearsGuard('craftman', $this->activate($craftman->user));
    }

    public function test_logout_clears_multiple_active_guards(): void
    {
        $webUser = $this->activeUser();
        $admin = $this->activeAdmin();
        $customer = Customer::factory()->create();
        $craftman = Craftman::factory()->create();

        auth('web')->login($webUser);
        auth('admin')->login($admin);
        auth('customer')->login($this->activate($customer->user));
        auth('craftman')->login($this->activate($craftman->user));

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertAllGuardsAreGuests();
    }

    public function test_authenticated_users_cannot_access_login_through_their_guard(): void
    {
        $admin = $this->activeAdmin();
        $this->actingAs($admin, 'admin')
            ->get('/login')
            ->assertRedirect('/cms/admin');

        auth('admin')->logout();

        $customer = Customer::factory()->create();
        $this->actingAs($this->activate($customer->user), 'customer')
            ->get('/login')
            ->assertRedirect('/herafy/main');

        auth('customer')->logout();

        $craftman = Craftman::factory()->create();
        $this->actingAs($this->activate($craftman->user), 'craftman')
            ->get('/login')
            ->assertRedirect(route('herafy.craftman.dashboard'));
    }

    public function test_profileless_user_without_an_admin_role_cannot_login(): void
    {
        $this->login($this->activeUser())->assertForbidden();

        $this->assertAllGuardsAreGuests();
    }

    public function test_customer_mapping_with_a_missing_profile_cannot_login(): void
    {
        $user = $this->activeUser([
            'actor_type' => Customer::class,
            'actor_id' => 999999,
        ]);

        $this->login($user)->assertForbidden();

        $this->assertAllGuardsAreGuests();
    }

    public function test_craftman_mapping_with_a_missing_profile_cannot_login(): void
    {
        $user = $this->activeUser([
            'actor_type' => Craftman::class,
            'actor_id' => 999999,
        ]);

        $this->login($user)->assertForbidden();

        $this->assertAllGuardsAreGuests();
    }

    public function test_unknown_actor_type_cannot_fall_back_to_admin_login(): void
    {
        $user = $this->activeUser([
            'actor_type' => 'App\\Models\\UnknownActor',
            'actor_id' => 1,
        ]);

        $this->login($user)->assertForbidden();

        $this->assertAllGuardsAreGuests();
    }

    private function login(User $user, bool $remember = false)
    {
        return $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => $remember,
        ]);
    }

    private function activeUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
            'password' => Hash::make('password'),
        ], $attributes));
    }

    private function activeAdmin(array $attributes = []): User
    {
        $admin = $this->activeUser($attributes);
        $role = Role::firstOrCreate([
            'name' => 'test-admin',
            'guard_name' => 'admin',
        ]);
        $admin->roles()->attach($role);

        return $admin;
    }

    private function activate(User $user): User
    {
        $user->forceFill([
            'status' => 'active',
            'password' => Hash::make('password'),
        ])->save();

        return $user->refresh();
    }

    private function assertLogoutClearsGuard(string $guard, User $user): void
    {
        $this->actingAs($user, $guard);

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertAllGuardsAreGuests();
    }

    private function assertAllGuardsAreGuests(): void
    {
        foreach (['web', 'admin', 'customer', 'craftman'] as $guard) {
            $this->assertGuest($guard);
        }
    }
}
