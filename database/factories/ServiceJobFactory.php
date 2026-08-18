<?php

namespace Database\Factories;

use App\Models\Craftman;
use App\Models\ServiceJob;
use App\Models\Order;
use App\Models\Offer;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceJobFactory extends Factory
{
    protected $model = ServiceJob::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'offer_id' => Offer::factory(),
            'customer_id' => Customer::factory(),
            'craftman_id' => Craftman::factory(),

            'status' => fake()->randomElement([
                'pending',
                'in_progress',
                'completed',
                'cancelled',
            ]),

            'started_at' => fake()->optional()->dateTimeBetween(
                '-1 month',
                'now'
            ),

            'completed_at' => null,
        ];
    }
}