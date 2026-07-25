<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\City;
use App\Models\Area;
use App\Models\Customer;


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
        'customer_id' => Customer::factory(),
        'city_id' => City::factory(),
        'area_id' => Area::factory(),

        'title' => fake()->sentence(3),

        'description' => fake()->paragraph(),

        'budget' => fake()->numberBetween(50,1000),

        'preferred_date' => fake()->dateTimeBetween('now','+2 months'),

        'status' => fake()->randomElement([
            'pending',
            'accepted',
            'in_progress',
            'completed',
            'cancelled'
        ]),

        'address' => fake()->address(),

        'phone' => fake()->phoneNumber(),
    ];
    }
}
