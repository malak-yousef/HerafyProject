<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
            'craftsman_id' => Craftman::factory(),
            'customer_id' => Customer::factory(),
            'request_id' => Order::factory(),
            'status' => fake()->randomElement([
                'pending',
                'approved',
                'rejected',
            ]),
        ];
    }
}
