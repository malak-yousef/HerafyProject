<?php

namespace Database\Seeders;

use App\Models\ServiceJob;
use Illuminate\Database\Seeder;

class ServiceJobSeeder extends Seeder
{
    public function run(): void
    {
        ServiceJob::factory()
            ->count(20)
            ->create();
    }
}
