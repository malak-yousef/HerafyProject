<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_limited_after_five_attempts_per_minute(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10']);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/login', [
                'email' => 'unknown@example.com',
                'password' => 'incorrect-password',
            ])->assertStatus(400);
        }

        $this->postJson('/login', [
            'email' => 'unknown@example.com',
            'password' => 'incorrect-password',
        ])->assertTooManyRequests();
    }

    public function test_registration_is_limited_after_five_attempts_per_minute(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.11']);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/register', [])->assertUnprocessable();
        }

        $this->postJson('/register', [])->assertTooManyRequests();
    }
}
