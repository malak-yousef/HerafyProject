<?php

namespace Database\Seeders;

use App\Models\PortfolioImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortfolioImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PortfolioImage::factory(5)->create();
    }
}
