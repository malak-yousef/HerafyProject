<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    public function test_404_page_renders_herafy_rtl_custom_view(): void
    {
        $response = $this->get('/non-existent-route-path-12345');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('الصفحة التي تبحث عنها غير موجودة');
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('حرفي');
    }
}
