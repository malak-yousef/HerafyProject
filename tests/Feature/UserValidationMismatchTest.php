<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserValidationMismatchTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::factory()->admin('super-admin')->create(['status' => 'active']);
    }

    public function test_admin_can_create_customer_with_nullable_mobile_and_address(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'admin')
            ->post('/cms/admin/customers', [
                'name' => 'عميل جديد',
                'user_name' => 'customer_null_test',
                'email' => 'customer_null@example.com',
                'password' => 'Password123!',
                'id_number' => '1234567890',
                'gender' => 'male',
                'status' => 'active',
                'mobile' => '',
                'address' => '',
            ]);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('users', [
            'user_name' => 'customer_null_test',
            'mobile' => null,
            'address' => null,
        ]);
    }

    public function test_public_registration_requires_mobile_address_gender(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'مستخدم جديد',
            'user_name' => 'public_user_test',
            'email' => 'public_user@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mobile', 'address', 'gender']);
    }
}
