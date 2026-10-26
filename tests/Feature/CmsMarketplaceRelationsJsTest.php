<?php

namespace Tests\Feature;

use Tests\TestCase;

class CmsMarketplaceRelationsJsTest extends TestCase
{
    public function test_shared_script_has_idempotent_plain_js_relationship_initializers(): void
    {
        $script = file_get_contents(public_path('cms/js/marketplace-relations.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString('initOffer', $script);
        $this->assertStringContainsString('initServiceJob', $script);
        $this->assertStringContainsString('relationshipFilterInitialized', $script);
        $this->assertStringContainsString("addEventListener('change'", $script);
        $this->assertStringContainsString('option.hidden = !available', $script);
        $this->assertStringContainsString('option.disabled = !available', $script);
        $this->assertStringContainsString("'اختر الطلب أولاً'", $script);
        $this->assertStringContainsString("'لا يوجد حرفيون متوافقون'", $script);
        $this->assertStringContainsString("'لا توجد عروض مرتبطة بهذا الطلب'", $script);
        $this->assertStringNotContainsString('innerHTML', $script);
        $this->assertStringNotContainsString('localStorage', $script);
        $this->assertStringNotContainsString('fetch(', $script);
    }
}
