<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_loads_for_guests(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_link_request_is_safe_and_sends_notification_for_existing_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $existing = $this->post(route('password.email'), ['email' => $user->email]);
        $unknown = $this->post(route('password.email'), ['email' => 'unknown@example.com']);

        $existing->assertRedirect()->assertSessionHas('status');
        $unknown->assertRedirect()->assertSessionHas('status');
        $this->assertSame($existing->getSession()->get('status'), $unknown->getSession()->get('status'));
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_form_loads_with_token_and_email(): void
    {
        $this->get(route('password.reset', ['token' => 'test-token', 'email' => 'user@example.com']))
            ->assertOk()
            ->assertViewHas('token', 'test-token')
            ->assertViewHas('email', 'user@example.com');
    }

    public function test_valid_token_resets_password_rotates_remember_token_and_cannot_be_reused(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123'),
            'remember_token' => Str::random(60),
            'status' => 'pending',
        ]);
        $oldRememberToken = $user->remember_token;
        $token = Password::broker()->createToken($user);

        $this->reset($user, $token)->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $user->password));
        $this->assertNotSame($oldRememberToken, $user->remember_token);
        $this->assertSame('pending', $user->status);

        $this->reset($user, $token)->assertSessionHasErrors('email');
    }

    public function test_invalid_and_expired_tokens_are_rejected(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123')]);

        $this->reset($user, 'invalid-token')->assertSessionHasErrors('email');

        $token = Password::broker()->createToken($user);
        $this->travel(61)->minutes();
        $this->reset($user, $token)->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_confirmation_mismatch_and_weak_password_are_rejected(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'DifferentPassword123',
        ])->assertSessionHasErrors('password');

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    public function test_new_password_uses_the_correct_guard_for_every_account_type(): void
    {
        $accounts = [
            [$this->admin(), 'admin'],
            [$this->activeActor(Customer::factory()->create()->user), 'customer'],
            [$this->activeActor(Craftman::factory()->create()->user), 'craftman'],
        ];

        foreach ($accounts as [$user, $guard]) {
            $token = Password::broker()->createToken($user);
            $this->reset($user, $token)->assertRedirect(route('login'));

            $this->postJson('/login', [
                'email' => $user->email,
                'password' => 'OldPassword123',
            ])->assertStatus(400);

            $this->postJson('/login', [
                'email' => $user->email,
                'password' => 'NewPassword123',
            ])->assertOk();
            $this->assertAuthenticatedAs($user, $guard);

            $this->post('/logout')->assertRedirect(route('login'));
        }
    }

    public function test_inactive_account_remains_inactive_after_reset(): void
    {
        $user = User::factory()->create(['status' => 'inactive']);
        $token = Password::broker()->createToken($user);

        $this->reset($user, $token)->assertRedirect(route('login'));

        $this->assertSame('inactive', $user->fresh()->status);
        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'NewPassword123',
        ])->assertForbidden();
    }

    public function test_authenticated_users_are_redirected_from_password_reset_pages(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin');

        $this->get(route('password.request'))->assertRedirect('/cms/admin');
        $this->get(route('password.reset', 'token'))->assertRedirect('/cms/admin');
        $this->post(route('password.email'), ['email' => $admin->email])->assertRedirect('/cms/admin');
        $this->post(route('password.update'), [])->assertRedirect('/cms/admin');

        auth('admin')->logout();
        $webUser = User::factory()->create();
        $this->actingAs($webUser, 'web');
        $this->get(route('password.request'))->assertRedirect('/');
        $this->get(route('password.reset', 'token'))->assertRedirect('/');
    }

    private function reset(User $user, string $token)
    {
        return $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);
    }

    private function admin(): User
    {
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
            'password' => Hash::make('OldPassword123'),
        ]);
        $role = Role::create(['name' => 'password-reset-admin', 'guard_name' => 'admin']);
        $admin->roles()->attach($role);

        return $admin;
    }

    private function activeActor(User $user): User
    {
        $user->forceFill([
            'status' => 'active',
            'password' => Hash::make('OldPassword123'),
        ])->save();

        return $user->refresh();
    }
}
