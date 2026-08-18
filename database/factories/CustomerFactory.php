<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'id_number' => fake()->unique()->numerify('############'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Customer $customer) {

            $customer->user()->create([
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