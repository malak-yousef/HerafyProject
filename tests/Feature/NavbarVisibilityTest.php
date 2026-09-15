<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_only_guest_account_actions(): void
    {
        $this->get(route('index'))
            ->assertOk()
            ->assertSee('دخول')
            ->assertSee('انضم الآن')
            ->assertDontSee('تسجيل الخروج')
            ->assertDontSee('لوحة التحكم')
            ->assertDontSee('الملف الشخصي')
            ->assertDontSee('طلباتي')
            ->assertDontSee('أعمالي');
    }

    public function test_customer_sees_customer_navigation_and_secure_logout(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer->user, 'customer')
            ->get(route('index'))
            ->assertOk()
            ->assertDontSee('>دخول<', false)
            ->assertDontSee('انضم الآن')
            ->assertSee('طلب خدمة')
            ->assertSee('طلباتي')
            ->assertSee('الحرفيون')
            ->assertSee('لوحة التحكم')
            ->assertSee('href="'.route('main').'"', false)
            ->assertSee('عرض الملف الشخصي')
            ->assertSee('href="'.route('herafy.customer.profile.show').'"', false)
            ->assertSee('تعديل الملف الشخصي')
            ->assertSee('href="'.route('herafy.customer.profile.edit').'"', false)
            ->assertSeeInOrder(['لوحة التحكم', 'عرض الملف الشخصي', 'تعديل الملف الشخصي', 'تسجيل الخروج'])
            ->assertSee('تسجيل الخروج')
            ->assertSee('action="'.route('logout').'"', false)
            ->assertDontSee('الطلبات المتاحة');
    }

    public function test_craftman_sees_only_craftman_navigation(): void
    {
        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('index'))
            ->assertOk()
            ->assertDontSee('>دخول<', false)
            ->assertDontSee('انضم الآن')
            ->assertSee('الطلبات المتاحة')
            ->assertSee('عروضي')
            ->assertSee('أعمالي')
            ->assertSee('التوثيق')
            ->assertSee('لوحة التحكم')
            ->assertSee('href="'.route('herafy.craftman.dashboard').'"', false)
            ->assertSee('عرض الملف الشخصي')
            ->assertSee('href="'.route('herafy.craftman.profile.show').'"', false)
            ->assertSee('تعديل الملف الشخصي')
            ->assertSee('href="'.route('herafy.craftman.profile.edit').'"', false)
            ->assertSeeInOrder(['لوحة التحكم', 'عرض الملف الشخصي', 'تعديل الملف الشخصي', 'تسجيل الخروج'])
            ->assertSee('تسجيل الخروج')
            ->assertDontSee('طلباتي')
            ->assertDontSee('طلب خدمة');
    }

    public function test_logout_is_post_only(): void
    {
        $this->get('/logout')->assertMethodNotAllowed();

        $logoutRoute = collect(app('router')->getRoutes()->getRoutes())
            ->first(fn ($route) => $route->getName() === 'logout');

        $this->assertSame(['POST'], $logoutRoute->methods());
    }
}
