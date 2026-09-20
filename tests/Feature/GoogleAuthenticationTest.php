<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_buttons_use_the_shared_centered_style(): void
    {
        foreach ([route('login'), route('register')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee(asset('herafy/css/google-auth.css'), false)
                ->assertSee('google-auth-btn', false)
                ->assertSee(route('auth.google.redirect'), false);
        }
    }

    public function test_existing_customer_can_login_with_google(): void
    {
        $customer = Customer::factory()->active()->create();
        $googleUser = $this->googleUser('google-existing', $customer->user->email, $customer->user->name);

        $this->mockGoogleCallback($googleUser);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(url('herafy/main'));

        $this->assertAuthenticatedAs($customer->user, 'customer');
        $this->assertSame('google-existing', $customer->user->fresh()->google_id);
        $this->assertNotNull($customer->user->fresh()->email_verified_at);
    }

    public function test_new_google_user_completes_customer_registration_without_a_password(): void
    {
        $googleUser = $this->googleUser('google-new', 'new-google@example.com', 'Ahmad Google');

        $this->mockGoogleCallback($googleUser);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('register'))
            ->assertSessionHas('google_registration');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('new-google@example.com')
            ->assertSee('تم التحقق من حساب Google');

        $response = $this->postJson(route('register'), [
            'name' => 'Changed Name',
            'user_name' => 'ahmad_google',
            'email' => 'forged@example.com',
            'mobile' => '0599000000',
            'address' => 'غزة',
            'gender' => 'male',
            'account_type' => 'user',
        ]);

        $response->assertOk()
            ->assertJsonPath('title', 'تم إنشاء الحساب عبر Google بنجاح')
            ->assertJsonPath('redirect', url('herafy/main'));

        $user = User::where('email', 'new-google@example.com')->firstOrFail();

        $this->assertSame('google-new', $user->google_id);
        $this->assertSame('Ahmad Google', $user->name);
        $this->assertAuthenticatedAs($user, 'customer');
        $this->assertFalse(session()->has('google_registration'));
    }

    public function test_inactive_account_cannot_login_with_google(): void
    {
        $customer = Customer::factory()->inactive()->create();

        $this->mockGoogleCallback(
            $this->googleUser('google-inactive', $customer->user->email, $customer->user->name)
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');

        $this->assertGuest('customer');
    }

    public function test_deleted_customer_must_be_restored_before_google_login(): void
    {
        $customer = Customer::factory()->active()->create();
        $user = $customer->user;
        $customer->delete();

        $this->mockGoogleCallback($this->googleUser('google-deleted', $user->email, $user->name));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'google' => 'هذا الحساب محذوف. اطلب من الإدارة استعادته من سلة المحذوفات ثم حاول مجددًا.',
            ]);

        $this->assertGuest('customer');
        $this->assertSoftDeleted($customer);
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_google_can_start_fresh_registration_after_legacy_permanent_deletion(): void
    {
        $customer = Customer::factory()->active()->create();
        $user = $customer->user;
        $user->forceFill(['google_id' => 'google-recreate'])->save();
        $customer->forceDelete();

        $this->mockGoogleCallback($this->googleUser('google-recreate', $user->email, $user->name));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('register'))
            ->assertSessionHas('google_registration');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertGuest('customer');
    }

    public function test_google_response_without_a_email_fails_gracefully(): void
    {
        $googleUser = $this->googleUser('google-no-email', '', 'No Email User');

        $this->mockGoogleCallback($googleUser);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['google']);

        $this->assertGuest('customer');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_google_oauth_failure_redirects_to_login_with_friendly_error(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new \RuntimeException('User denied the authorization request.'));

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'google' => 'تعذر تسجيل الدخول عبر Google. يرجى المحاولة مرة أخرى.',
            ]);

        $this->assertGuest('customer');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_google_redirect_is_unavailable_when_configuration_is_missing(): void
    {
        config()->set('services.google.client_id', null);
        config()->set('services.google.client_secret', null);
        config()->set('services.google.redirect', null);

        $this->get(route('auth.google.redirect'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'google' => 'تسجيل الدخول عبر Google غير متاح حالياً.',
            ]);
    }

    private function googleUser(string $id, string $email, string $name): GoogleUser
    {
        $user = new GoogleUser;
        $user->id = $id;
        $user->email = $email;
        $user->name = $name;

        return $user;
    }

    private function mockGoogleCallback(GoogleUser $googleUser): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
    }
}
