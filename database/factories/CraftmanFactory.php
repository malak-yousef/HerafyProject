<?php

namespace Database\Factories;

use App\Models\Craftman;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Craftman> */
class CraftmanFactory extends Factory
{
    protected $model = Craftman::class;

    public function definition(): array
    {
        return [
            'profession_id' => Profession::factory()->active(),
            'id_number' => fake()->unique()->numerify('##########'),
            'experience_years' => fake()->numberBetween(1, 25),
            'description' => fake()->randomElement([
                'حرفي متخصص في أعمال الصيانة المنزلية مع الالتزام بالمواعيد.',
                'خبرة عملية في تنفيذ المشاريع المنزلية والتجارية بجودة عالية.',
                'أقدم خدمة مهنية موثوقة وأسعاراً واضحة قبل بدء العمل.',
            ]),
            'rating' => 0,
            'total_reviews' => 0,
            'hourly_rate' => fake()->numberBetween(30, 150),
            'daily_rate' => fake()->numberBetween(150, 600),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Craftman $craftman): void {
            if (! $craftman->user()->exists()) {
                User::factory()->create([
                    'actor_type' => Craftman::class,
                    'actor_id' => $craftman->id,
                ]);
            }
        });
    }

    public function active(): static
    {
        return $this->afterCreating(fn (Craftman $craftman) => $craftman->user()->update(['status' => 'active']));
    }

    public function inactive(): static
    {
        return $this->afterCreating(fn (Craftman $craftman) => $craftman->user()->update(['status' => 'inactive']));
    }

    public function blocked(): static
    {
        return $this->afterCreating(fn (Craftman $craftman) => $craftman->user()->update(['status' => 'blocked']));
    }
}
