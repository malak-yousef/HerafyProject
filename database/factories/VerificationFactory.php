<?php

namespace Database\Factories;

use App\Models\Craftman;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Verification> */
class VerificationFactory extends Factory
{
    protected $model = Verification::class;

    public function definition(): array
    {
        return [
            'craftman_id' => Craftman::factory()->active(),
            'document_path' => 'verifications/demo/'.fake()->uuid().'.pdf',
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
