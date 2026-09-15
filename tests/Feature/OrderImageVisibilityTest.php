<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Order;
use App\Models\OrderImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderImageVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_eligible_craftman_see_modern_order_images(): void
    {
        $craftman = Craftman::factory()->active()->create();
        $order = Order::factory()->pending()->create(['profession_id' => $craftman->profession_id]);
        $image = OrderImage::create([
            'order_id' => $order->id,
            'image_path' => "order-images/{$order->id}/request-photo.png",
        ]);

        $this->actingAs($order->customer->user, 'customer')
            ->get(route('herafy.customer.orders.show', $order))
            ->assertOk()
            ->assertSee($image->publicUrl(), false)
            ->assertSee('صور الطلب');

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.order.show', $order))
            ->assertOk()
            ->assertSee($image->publicUrl(), false)
            ->assertSee('صور الطلب');
    }

    public function test_image_url_supports_modern_and_legacy_database_paths(): void
    {
        $modern = new OrderImage(['image_path' => 'order-images/10/modern.png']);
        $legacy = new OrderImage(['image_path' => 'legacy.png']);

        $this->assertSame('/storage/order-images/10/modern.png', $modern->publicUrl());
        $this->assertSame('/storage/images/order_images/legacy.png', $legacy->publicUrl());
    }
}
