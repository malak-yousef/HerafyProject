<?php

namespace Database\Factories;

use App\Models\Craftman;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Offer> */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory()->pending(),
            'craftman_id' => fn (array $attributes) => Craftman::factory()->active()->create([
                'profession_id' => Order::findOrFail($attributes['order_id'])->profession_id,
            ])->id,
            'price' => fake()->numberBetween(100, 2200),
            'message' => 'يمكنني تنفيذ العمل بالمواصفات المطلوبة وفي الموعد المتفق عليه.',
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function accepted(): static
    {
        return $this->withSynchronizedStatus('accepted');
    }

    public function inProgress(): static
    {
        return $this->withSynchronizedStatus('in_progress');
    }

    public function completed(): static
    {
        return $this->withSynchronizedStatus('completed');
    }

    public function cancelled(): static
    {
        return $this->withSynchronizedStatus('cancelled');
    }

    private function withSynchronizedStatus(string $status): static
    {
        return $this->state(fn () => ['status' => $status])
            ->afterCreating(fn (Offer $offer) => $offer->order()->update(['status' => $status]));
    }
}
