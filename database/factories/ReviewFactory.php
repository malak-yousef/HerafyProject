<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Review;
use App\Models\ServiceJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Review> */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'request_id' => fn () => ServiceJob::factory()->completed()->create()->order_id,
            'craftsman_id' => fn (array $attributes) => Order::findOrFail($attributes['request_id'])->serviceJob->craftman_id,
            'customer_id' => fn (array $attributes) => Order::findOrFail($attributes['request_id'])->customer_id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional(0.85)->randomElement([
                'عمل ممتاز والتزام واضح بالموعد.',
                'الخدمة جيدة والتعامل محترم.',
                'تم إنجاز المطلوب بجودة عالية.',
                'تجربة موفقة وسأتعامل معه مرة أخرى.',
            ]),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
