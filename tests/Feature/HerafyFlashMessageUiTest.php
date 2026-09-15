<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HerafyFlashMessageUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_layout_renders_success_flash_through_shared_crud_messages(): void
    {
        $response = $this->withSession(['success' => 'تم تنفيذ العملية بنجاح.'])
            ->get(route('index'));

        $response->assertOk();
        $response->assertSee(asset('js/crud.js'), false);
        $response->assertSee('showMessage(', false);
        $response->assertSee(json_encode('تم تنفيذ العملية بنجاح.'), false);
    }

    public function test_public_layout_renders_error_flash_through_shared_crud_messages(): void
    {
        $response = $this->withSession(['error' => 'تعذر تنفيذ العملية.'])
            ->get(route('index'));

        $response->assertOk();
        $response->assertSee('showMessage(', false);
        $response->assertSee(json_encode('تعذر تنفيذ العملية.'), false);
    }
}
