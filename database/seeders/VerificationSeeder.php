<?php

namespace Database\Seeders;

use App\Models\Verification;
use App\Models\Craftman;
use Illuminate\Database\Seeder;

class VerificationSeeder extends Seeder
{
    public function run(): void
    {
        $craftmen = Craftman::all();

        if ($craftmen->isEmpty()) {
            $this->command->warn('لا يوجد حرفيون لإضافة طلبات التوثيق.');
            return;
        }

        foreach ($craftmen as $craftman) {

            Verification::create([
                'craftman_id' => $craftman->id,
                'document_path' => 'verifications/sample-document.pdf',
                'status' => fake()->randomElement([
                    'pending',
                    'approved',
                    'rejected',
                ]),
            ]);
        }
    }
}