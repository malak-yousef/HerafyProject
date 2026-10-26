<?php

namespace Database\Factories;

use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'portfolioCover_image' => fake()->imageUrl(),
            'location' => fake()->address(),
            'description' => fake()->text(),
            'craftman_id' => fake()->numberBetween(1, 5),
            'status' => fake()->boolean(),
        ];
    }
}
