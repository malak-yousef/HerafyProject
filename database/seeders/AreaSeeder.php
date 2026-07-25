<?php

namespace Database\Seeders;
use App\Models\Area;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // غزة
        $area1 = Area::create([
            'city_id' => 1,
            'name' => 'الرمال',
        ]);

        $area2 = Area::create([
            'city_id' => 1,
            'name' => 'تل الهوى',
        ]);

        $area3 = Area::create([
            'city_id' => 1,
            'name' => 'الشيخ رضوان',
        ]);

        $area4 = Area::create([
            'city_id' => 1,
            'name' => 'الشجاعية',
        ]);

        // شمال غزة
        $area5 = Area::create([
            'city_id' => 2,
            'name' => 'جباليا',
        ]);

        $area6 = Area::create([
            'city_id' => 2,
            'name' => 'بيت لاهيا',
        ]);

        $area7 = Area::create([
            'city_id' => 2,
            'name' => 'بيت حانون',
        ]);

        // دير البلح
        $area8 = Area::create([
            'city_id' => 3,
            'name' => 'الزوايدة',
        ]);

        $area9 = Area::create([
            'city_id' => 3,
            'name' => 'المغازي',
        ]);

        $area10 = Area::create([
            'city_id' => 3,
            'name' => 'النصيرات',
        ]);

        // خانيونس
        $area11 = Area::create([
            'city_id' => 4,
            'name' => 'بني سهيلا',
        ]);

        $area12 = Area::create([
            'city_id' => 4,
            'name' => 'عبسان',
        ]);

        $area13 = Area::create([
            'city_id' => 4,
            'name' => 'القرارة',
        ]);

        // رفح
        $area14 = Area::create([
            'city_id' => 5,
            'name' => 'الشوكة',
        ]);

        $area15 = Area::create([
            'city_id' => 5,
            'name' => 'تل السلطان',
        ]);
    }
}