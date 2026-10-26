<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CraftmanOfferCreateUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_requested_service_date_is_formatted_consistently(): void
    {
        $craftman = Craftman::factory()->create();
        $order = Order::factory()->create([
            'status' => 'pending',
            'profession_id' => $craftman->profession_id,
            'service_date' => '2026-03-15',
        ]);

        $content = $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.offer.create', $order))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('2026-03-15', $content);
        $this->assertStringNotContainsString(
            '2026-03-15 00:00:00',
            $content,
            'An unformatted Carbon date leaks its time component and breaks the Y-m-d convention.'
        );
    }

    public function test_offer_create_template_formats_the_service_date(): void
    {
        $content = file_get_contents(resource_path('views/herafy/offer/create.blade.php'));

        $this->assertStringContainsString("{{ \$order->service_date?->format('Y-m-d') ?? '-' }}", $content);
        $this->assertStringNotContainsString('{{ $order->service_date ??', $content);
    }

    public function test_missing_service_date_does_not_break_the_page(): void
    {
        $craftman = Craftman::factory()->create();
        $order = Order::factory()->create([
            'status' => 'pending',
            'profession_id' => $craftman->profession_id,
            'service_date' => null,
        ]);

        $response = $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.offer.create', $order))
            ->assertOk();

        $response->assertSee('التاريخ المطلوب');
        $this->assertStringNotContainsString('00:00:00', $response->getContent());
    }
}
