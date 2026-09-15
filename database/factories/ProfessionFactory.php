<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Profession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profession>
 */
class ProfessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // return [
            //
            'name' => fake()->name(),
            'description' => fake()->word(),
            'status' => fake()->randomElement([
                'active',
                'in_active',
            ]),
            'category_id' => Category::factory(),

        ];

    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'in_active']);
    }
}
