<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
/**
 * @extends Factory<OrderImage>
 */
class OrderImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'request_id'=>Order::factory(),

            'image_path'=>'orders/images/'
                .$this->faker->image()
        ];
    }
}
