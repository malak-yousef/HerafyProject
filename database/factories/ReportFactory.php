<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\ServiceJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Report> */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'job_id' => ServiceJob::factory()->completed(),
            'reporter_id' => fn (array $attributes) => ServiceJob::findOrFail($attributes['job_id'])->customer->user->id,
            'reported_id' => fn (array $attributes) => ServiceJob::findOrFail($attributes['job_id'])->craftman->user->id,
            'reason' => fake()->randomElement(['تأخر في الموعد', 'مشكلة في جودة التنفيذ', 'سوء التواصل', 'خلاف على تفاصيل الخدمة']),
            'description' => fake()->optional()->sentence(),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function reviewing(): static
    {
        return $this->state(fn () => ['status' => 'reviewing']);
    }

    public function resolved(): static
    {
        return $this->state(fn () => ['status' => 'resolved']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
