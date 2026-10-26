<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['شمال غزة', 'غزة', 'الوسطى', 'خانيونس', 'رفح'] as $name) {
            $city = City::withTrashed()->firstOrCreate(['name' => $name]);
            if ($city->trashed()) {
                $city->restore();
            }
        }
    }
}
