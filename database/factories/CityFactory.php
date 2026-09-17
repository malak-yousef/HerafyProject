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
            'name' => fake()->unique()->city().' '.fake()->unique()->numberBetween(1, 10000),
        ];
    }
}
