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
        $name = fake()->randomElement(self::GAZA_LOCATIONS);
        if (City::where('name', $name)->exists()) {
            $name = $name.' '.fake()->unique()->numberBetween(1, 9999);
        }

        return [
            'name' => $name,
        ];
    }
}
