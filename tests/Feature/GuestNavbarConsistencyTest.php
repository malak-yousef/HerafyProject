<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestNavbarConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_login_and_register_use_the_same_guest_navbar(): void
    {
        foreach ([route('index'), route('login'), route('register')] as $url) {
            $content = $this->get($url)->assertOk()->getContent();

            $this->assertSame(1, substr_count($content, 'herafy-navbar'));
            $this->assertStringContainsString(asset('herafy/css/navbar.css'), $content);
            $this->assertStringContainsString('href="'.route('index').'"', $content);
            $this->assertStringContainsString('href="'.route('index').'#about"', $content);
            $this->assertStringContainsString('href="'.route('showCraftsmen').'"', $content);
            $this->assertStringContainsString('href="'.route('index').'#testimonials"', $content);
            $this->assertStringContainsString('href="'.route('index').'#gallery"', $content);
            $this->assertStringContainsString('href="'.route('login').'"', $content);
            $this->assertStringContainsString('href="'.route('register').'"', $content);
            $this->assertStringNotContainsString('href="index.html"', $content);
        }
    }

    public function test_authenticated_navigation_keeps_account_specific_controls(): void
    {
        $customer = Customer::factory()->active()->create();

        $this->actingAs($customer->user, 'customer')
            ->get(route('main'))
            ->assertOk()
            ->assertSee('طلباتي')
            ->assertSee('تسجيل الخروج');
    }
}
