<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use App\Models\ServiceJob;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id')->toArray();

        $jobs = ServiceJob::pluck('id')->toArray();

        if (count($users) < 2) {
            $this->command->warn(
                'يجب وجود مستخدمين اثنين على الأقل لإنشاء البلاغات.'
            );

            return;
        }

        if (empty($jobs)) {
            $this->command->warn(
                'لا يوجد Service Jobs لإنشاء البلاغات.'
            );

            return;
        }

        for ($i = 0; $i < 10; $i++) {

            $reporterId = fake()->randomElement($users);

            $availableUsers = array_diff($users, [$reporterId]);

            $reportedId = fake()->randomElement($availableUsers);

            Report::create([

                'reporter_id' => $reporterId,

                'reported_id' => $reportedId,

                'job_id' => fake()->randomElement($jobs),

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
            ]);
        }
    }
}