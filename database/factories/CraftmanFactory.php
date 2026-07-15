<?php

namespace Database\Factories;

use App\Models\Craftman;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Craftman>
 */
class CraftmanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'name' => fake()->name(),
            // 'craft_name' => fake()->name(),
            // 'id_number' => fake()->unique()->regexify('[A-Z]{3}[0-9]{6}'),
            // 'experience_years'=> fake()->numberBetween(1, 30),
            // 'description'=> fake()->text(),
            // 'rating'=> fake()->randomFloat(2, 0, 5),
            // 'total_reviews'=> fake()->numberBetween(0, 1000),
            // 'hourly_rate'=> fake()->numberBetween(0, 1000),
            // 'daily_rate'=> fake()->numberBetween(0, 1000),
            // 'profile_image'=> fake()->imageUrl(),
        ];
    }
}