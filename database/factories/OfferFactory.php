<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Craftman;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'craftman_id' => Craftman::factory(),
            'price' => fake()->numberBetween(100, 2000),
            'message' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}