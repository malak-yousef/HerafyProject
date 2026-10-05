<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HerafyFooterNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_does_not_show_fabricated_contact_details(): void
    {
        $content = $this->get(route('index'))->assertOk()->getContent();

        $this->assertStringNotContainsString('012 345 6789', $content);
        $this->assertStringNotContainsString('info@harfi.com', $content);
        $this->assertStringNotContainsString('تواصل معنا', $content);
    }

    public function test_footer_links_point_to_real_destinations_only(): void
    {
        $content = $this->get(route('index'))->assertOk()->getContent();

        foreach ([
            route('index'),
            route('index').'#about',
            route('showCraftsmen'),
            route('index').'#testimonials',
            route('register'),
            route('login'),
            route('password.request'),
        ] as $destination) {
            $this->assertStringContainsString('href="'.$destination.'"', $content);
        }

        $this->assertStringNotContainsString('href="#"', $content);
    }

    public function test_footer_shows_account_links_matching_the_visitor_guard(): void
    {
        $guest = $this->get(route('index'))->assertOk()->getContent();
        $this->assertStringContainsString('href="'.route('password.request').'"', $guest);

        $customerContent = $this->actingAs(Customer::factory()->create()->user, 'customer')
            ->get(route('main'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('href="'.route('herafy.customer.orders.index').'"', $customerContent);
        $this->assertStringContainsString('href="'.route('herafy.customer.notifications.index').'"', $customerContent);
        $this->assertStringNotContainsString('href="'.route('register').'"', $customerContent);

        auth('customer')->logout();

        $craftmanContent = $this->actingAs(Craftman::factory()->create()->user, 'craftman')
            ->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('href="'.route('herafy.craftman.dashboard').'"', $craftmanContent);
        $this->assertStringContainsString('href="'.route('herafy.craftman.jobs.index').'"', $craftmanContent);
        $this->assertStringNotContainsString('href="'.route('login').'"', $craftmanContent);
    }

    public function test_customer_home_navigation_opens_the_customer_dashboard(): void
    {
        $customer = Customer::factory()->create();

        $content = $this->actingAs($customer->user, 'customer')
            ->get(route('main'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(
            'href="'.route('main').'"',
            $content,
            'A signed-in customer must reach their dashboard from the Home navigation item.'
        );
    }
}
