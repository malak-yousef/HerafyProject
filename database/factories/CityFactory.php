<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<City> */
class CityFactory extends Factory
{
    public const GAZA_LOCATIONS = ['شمال غزة', 'غزة', 'الوسطى', 'خانيونس', 'رفح'];

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' '.fake()->unique()->numberBetween(100, 99999),
        ];
    }
}
