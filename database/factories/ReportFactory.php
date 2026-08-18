<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use App\Models\ServiceJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $users = User::pluck('id')->toArray();

        $reporterId = fake()->randomElement($users);

        $reportedId = fake()->randomElement(
            array_diff($users, [$reporterId])
        );

        return [
            'reporter_id' => $reporterId,
            'reported_id' => $reportedId,
            'job_id' => ServiceJob::inRandomOrder()->value('id'),

            'reason' => fake()->randomElement([
                'سوء التعامل',
                'عدم الالتزام بالاتفاق',
                'خدمة سيئة',
                'تأخير في تنفيذ العمل',
                'مشكلة في التواصل',
            ]),

            'description' => fake()->paragraph(),

            'status' => fake()->randomElement([
                'pending',
                'reviewing',
                'resolved',
                'rejected',
            ]),
        ];
    }
}