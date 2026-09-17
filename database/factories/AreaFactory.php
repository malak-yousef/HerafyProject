<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Area> */
class AreaFactory extends Factory
{
    public const GAZA_AREAS = [
        'شمال غزة' => ['بيت لاهيا', 'بيت حانون', 'جباليا', 'مخيم جباليا', 'مشروع بيت لاهيا', 'العطاطرة', 'السلاطين', 'أم النصر'],
        'غزة' => ['الرمال', 'النصر', 'الشجاعية', 'الزيتون', 'التفاح', 'الدرج', 'الصبرة', 'تل الهوى', 'الشيخ رضوان', 'الرمال الشمالي', 'الرمال الجنوبي', 'الميناء'],
        'الوسطى' => ['دير البلح', 'النصيرات', 'مخيم النصيرات', 'البريج', 'مخيم البريج', 'المغازي', 'مخيم المغازي', 'الزوايدة'],
        'خانيونس' => ['خانيونس البلد', 'بني سهيلا', 'عبسان الكبيرة', 'عبسان الجديدة', 'خزاعة', 'القرارة', 'الفخاري', 'معن', 'جورة اللوت', 'المواصي', 'مخيم خانيونس'],
        'رفح' => ['رفح البلد', 'تل السلطان', 'الشابورة', 'الجنينة', 'يبنا', 'مصبح', 'حي السلام', 'خربة العدس', 'المواصي - رفح'],
    ];

    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'name' => function (array $attributes) {
                $cityId = $attributes['city_id'] ?? null;
                $city = $cityId ? City::find($cityId) : null;
                $cityName = $city?->name ?? 'غزة';
                $baseCityName = preg_replace('/\s+\d+$/', '', $cityName);
                $available = self::GAZA_AREAS[$baseCityName] ?? self::GAZA_AREAS['غزة'];

                $existingNames = $cityId ? Area::where('city_id', $cityId)->pluck('name')->all() : [];
                $unused = array_values(array_diff($available, $existingNames));

                if (! empty($unused)) {
                    return fake()->randomElement($unused);
                }

                return fake()->randomElement($available).' '.fake()->unique()->numberBetween(1, 9999);
            },
        ];
    }
}
