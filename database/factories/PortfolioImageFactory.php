<?php

namespace Database\Factories;

use App\Models\PortfolioImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioImage>
 */
class PortfolioImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_path' => fake()->imageUrl(),
            'image_name' => fake()->word(),
            'image_description' => fake()->sentence(),
            'portfolio_id' => fake()->numberBetween(1, 5),
        ];
    }
}
