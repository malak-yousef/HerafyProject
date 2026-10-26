<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return ['id_number' => fake()->unique()->numerify('##########')];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Customer $customer): void {
            if (! $customer->user()->exists()) {
                User::factory()->create([
                    'actor_type' => Customer::class,
                    'actor_id' => $customer->id,
                ]);
            }
        });
    }

    public function active(): static
    {
        return $this->afterCreating(fn (Customer $customer) => $customer->user()->update(['status' => 'active']));
    }

    public function inactive(): static
    {
        return $this->afterCreating(fn (Customer $customer) => $customer->user()->update(['status' => 'inactive']));
    }

    public function blocked(): static
    {
        return $this->afterCreating(fn (Customer $customer) => $customer->user()->update(['status' => 'blocked']));
    }
}
