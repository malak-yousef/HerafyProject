<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_page_does_not_require_hidden_craftsman_fields(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('id="id_number"', false)
            ->assertDontSee('id="id_number" required', false);
    }

    public function test_customer_registration_succeeds(): void
    {
        $response = $this->postJson('/register', $this->customerPayload());

        $response->assertOk()->assertJsonPath('redirect', route('login'));

        $user = User::where('email', 'customer@example.com')->firstOrFail();
        $this->assertSame(Customer::class, $user->actor_type);
        $this->assertInstanceOf(Customer::class, $user->actor);
        $this->assertSame($user->actor_id, $user->actor->getKey());
        $this->assertTrue($user->isCustomer());
        $this->assertTrue(Hash::check('StrongPass123', $user->password));
    }

    public function test_craftsman_registration_succeeds(): void
    {
        $profession = Profession::factory()->create(['status' => 'active']);

        $response = $this->postJson('/register', $this->craftsmanPayload($profession));

        $response->assertOk()->assertJsonPath('redirect', route('login'));

        $user = User::where('email', 'craftsman@example.com')->firstOrFail();
        $this->assertSame(Craftman::class, $user->actor_type);
        $this->assertInstanceOf(Craftman::class, $user->actor);
        $this->assertSame('123456789', $user->actor->id_number);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'customer@example.com']);

        $this->postJson('/register', $this->customerPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_duplicate_username_is_rejected(): void
    {
        User::factory()->create(['user_name' => 'new_customer']);

        $this->postJson('/register', $this->customerPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('user_name');
    }

    public function test_duplicate_craftsman_id_number_is_rejected(): void
    {
        $existing = Craftman::factory()->create();
        $profession = Profession::factory()->create(['status' => 'active']);
        $payload = $this->craftsmanPayload($profession, [
            'id_number' => $existing->id_number,
        ]);

        $this->postJson('/register', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('id_number');
    }

    public function test_missing_required_customer_fields_return_validation_errors(): void
    {
        $payload = $this->customerPayload();
        foreach (['name', 'user_name', 'email', 'password', 'password_confirmation', 'mobile', 'address', 'gender'] as $field) {
            unset($payload[$field]);
        }

        $this->postJson('/register', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name', 'user_name', 'email', 'password', 'mobile', 'address', 'gender',
            ]);
    }

    public function test_missing_required_craftsman_fields_return_validation_errors(): void
    {
        $payload = $this->customerPayload([
            'account_type' => 'craftsman',
            'email' => 'craftsman@example.com',
            'user_name' => 'new_craftsman',
        ]);

        $this->postJson('/register', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['id_number', 'profession_id', 'experience_years']);
    }

    public function test_weak_password_is_rejected(): void
    {
        $this->postJson('/register', $this->customerPayload([
            'password' => 'short',
            'password_confirmation' => 'short',
        ]))->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_password_confirmation_mismatch_is_rejected(): void
    {
        $this->postJson('/register', $this->customerPayload([
            'password_confirmation' => 'DifferentPass123',
        ]))->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_invalid_account_type_is_rejected(): void
    {
        $this->postJson('/register', $this->customerPayload([
            'account_type' => 'admin',
        ]))->assertUnprocessable()->assertJsonValidationErrors('account_type');
    }

    public function test_failed_registration_rolls_back_profile_and_user_records(): void
    {
        User::creating(function (): void {
            throw new RuntimeException('Forced user creation failure.');
        });

        $this->postJson('/register', $this->customerPayload())
            ->assertInternalServerError()
            ->assertJsonMissing(['title' => 'Forced user creation failure.']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('customers', 0);
        $this->assertDatabaseCount('craftmen', 0);
    }

    public function test_authenticated_users_cannot_access_or_submit_registration(): void
    {
        $admin = User::factory()->create(['status' => 'active']);
        $role = Role::create(['name' => 'test-admin', 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $this->actingAs($admin, 'admin');
        $this->get('/register')->assertRedirect('/cms/admin');
        $this->postJson('/register', $this->customerPayload())->assertRedirect('/cms/admin');
        auth('admin')->logout();

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer');
        $this->get('/register')->assertRedirect('/herafy/main');
        $this->postJson('/register', $this->customerPayload())->assertRedirect('/herafy/main');
        auth('customer')->logout();

        $craftman = Craftman::factory()->create();
        $this->actingAs($craftman->user, 'craftman');
        $this->get('/register')->assertRedirect('/herafy/main');
        $this->postJson('/register', $this->customerPayload())->assertRedirect('/herafy/main');
    }

    private function customerPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'New Customer',
            'user_name' => 'new_customer',
            'email' => 'customer@example.com',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
            'mobile' => '0599999999',
            'address' => 'Gaza',
            'gender' => 'male',
            'account_type' => 'user',
        ], $overrides);
    }

    private function craftsmanPayload(Profession $profession, array $overrides = []): array
    {
        return $this->customerPayload(array_merge([
            'user_name' => 'new_craftsman',
            'email' => 'craftsman@example.com',
            'account_type' => 'craftsman',
            'id_number' => '123456789',
            'profession_id' => $profession->id,
            'experience_years' => 5,
            'hourly_rate' => 50,
            'daily_rate' => 300,
            'description' => 'Experienced craftsperson.',
        ], $overrides));
    }
}
