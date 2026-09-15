<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Order;
use App\Models\ServiceJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ServiceJob> */
class ServiceJobFactory extends Factory
{
    protected $model = ServiceJob::class;

    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory()->accepted(),
            'order_id' => fn (array $attributes) => Offer::findOrFail($attributes['offer_id'])->order_id,
            'customer_id' => fn (array $attributes) => Order::findOrFail($attributes['order_id'])->customer_id,
            'craftman_id' => fn (array $attributes) => Offer::findOrFail($attributes['offer_id'])->craftman_id,
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ServiceJob $job): void {
            $linkedStatus = $job->status === 'pending' ? 'accepted' : $job->status;
            $job->order()->update(['status' => $linkedStatus]);
            $job->offer()->update(['status' => $linkedStatus]);
        });
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending', 'started_at' => null, 'completed_at' => null]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => 'in_progress',
            'started_at' => fake()->dateTimeBetween('-20 days', '-1 day'),
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        $startedAt = fake()->dateTimeBetween('-30 days', '-3 days');

        return $this->state(fn () => [
            'status' => 'completed',
            'started_at' => $startedAt,
            'completed_at' => fake()->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled', 'started_at' => null, 'completed_at' => null]);
    }
}
