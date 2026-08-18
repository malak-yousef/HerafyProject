<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'max_daily_offers',
                'value' => '10',
            ],
            [
                'key' => 'max_project_images',
                'value' => '5',
            ],
            [
                'key' => 'max_offer_images',
                'value' => '3',
            ],
            [
                'key' => 'site_name',
                'value' => 'حرفي',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}