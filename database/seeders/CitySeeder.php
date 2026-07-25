<?php

namespace Database\Seeders;
use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $cityname1 = City::create([
    'name' => 'غزة',
          ]);
          $cityname2 = City::create([
    'name' => 'شمال غزة',
          ]);
          $cityname3 = City::create([
    'name' => 'دير البلح',
          ]);
          $cityname4 = City::create([
    'name' => 'خانيونس',
          ]);
           $cityname5 = City::create([
    'name' => 'رفح',
          ]);
    }
}
