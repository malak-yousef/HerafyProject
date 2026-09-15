<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'شمال غزة' => ['بيت لاهيا', 'بيت حانون', 'جباليا', 'مخيم جباليا', 'مشروع بيت لاهيا', 'العطاطرة', 'السلاطين', 'أم النصر'],
            'غزة' => ['الرمال', 'النصر', 'الشجاعية', 'الزيتون', 'التفاح', 'الدرج', 'الصبرة', 'تل الهوى', 'الشيخ رضوان', 'الرمال الشمالي', 'الرمال الجنوبي', 'الميناء'],
            'الوسطى' => ['دير البلح', 'النصيرات', 'مخيم النصيرات', 'البريج', 'مخيم البريج', 'المغازي', 'مخيم المغازي', 'الزوايدة'],
            'خانيونس' => ['خانيونس البلد', 'بني سهيلا', 'عبسان الكبيرة', 'عبسان الجديدة', 'خزاعة', 'القرارة', 'الفخاري', 'معن', 'جورة اللوت', 'المواصي', 'مخيم خانيونس'],
            'رفح' => ['رفح البلد', 'تل السلطان', 'الشابورة', 'الجنينة', 'يبنا', 'مصبح', 'حي السلام', 'خربة العدس', 'المواصي - رفح'],
        ];

        foreach ($areas as $cityName => $names) {
            $city = City::where('name', $cityName)->firstOrFail();
            foreach ($names as $name) {
                $area = Area::withTrashed()->firstOrCreate(['city_id' => $city->id, 'name' => $name]);
                if ($area->trashed()) {
                    $area->restore();
                }
            }
        }
    }
}
