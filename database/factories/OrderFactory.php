<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\City;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Profession;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory()->active(),
            'profession_id' => Profession::factory()->active(),
            'city_id' => fn () => City::inRandomOrder()->first()?->id ?? City::factory()->create()->id,
            'area_id' => function (array $attributes) {
                $cityId = $attributes['city_id'];
                $area = Area::where('city_id', $cityId)->inRandomOrder()->first();
                if ($area) {
                    return $area->id;
                }

                return Area::factory()->create(['city_id' => $cityId])->id;
            },
            'title' => fake()->randomElement([
                'صيانة تمديدات كهربائية', 'إصلاح تسريب مياه', 'تركيب أبواب خشبية',
                'دهان شقة سكنية', 'صيانة جهاز تكييف', 'تنفيذ أعمال حديدية',
            ]),
            'description' => 'مطلوب حرفي متخصص لتنفيذ العمل بجودة عالية وفي الموعد المحدد.',
            'budget' => fake()->numberBetween(100, 2500),
            'service_date' => fake()->dateTimeBetween('+1 day', '+2 months')->format('Y-m-d'),
            'status' => 'pending',
            'address' => fake()->streetAddress(),
            'phone' => fake()->numerify('059#######'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => 'accepted']);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => 'in_progress']);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }
}
