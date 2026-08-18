<?php

namespace Database\Factories;

use App\Models\Craftman;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationFactory extends Factory
{
    protected $model = Verification::class;

    public function definition(): array
    {
        return [
            'craftman_id' => Craftman::factory(),

            'document_path' => 'verifications/sample-document.pdf',

            'status' => fake()->randomElement([
                'pending',
                'approved',
                'rejected',
            ]),
        ];
    }
}