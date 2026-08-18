<?php

namespace Database\Factories;

use App\Models\Craftman;
use App\Models\Profession;
use Illuminate\Database\Eloquent\Factories\Factory;

class CraftmanFactory extends Factory
{
    protected $model = Craftman::class;

    public function definition(): array
    {
        return [
            'profession_id' => Profession::factory(),

            'id_number' => fake()->unique()->numerify('##########'),

            'experience_years' => fake()->numberBetween(1, 20),

            'description' => fake()->paragraph(),

            'rating' => fake()->randomFloat(2, 0, 5),

            'total_reviews' => fake()->numberBetween(0, 500),

            'hourly_rate' => fake()->numberBetween(50, 500),

            'daily_rate' => fake()->numberBetween(200, 2000),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Craftman $craftman) {

            $craftman->user()->create([
                'name' => fake()->name(),

                'user_name' => fake()->unique()->userName(),

                'email' => fake()->unique()->safeEmail(),

                'password' => 'password',

                'mobile' => fake()->numerify('059########'),

                'gender' => fake()->randomElement([
                    'male',
                    'female',
                ]),

                'status' => 'active',

                'address' => fake()->address(),

                'date' => now(),
            ]);

        });
    }
}