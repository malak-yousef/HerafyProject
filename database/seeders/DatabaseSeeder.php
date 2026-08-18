<?php

namespace Database\Seeders;

use App\Models\OrderImage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\SettingSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

       
     
         $this->call([
              AdminSeeder::class,
              CustomerSeeder::class,
               CategorySeeder::class,
               ProfessionSeeder::class,
              CraftmanSeeder::class,

    ServiceJobSeeder::class,
         OrderImageSeeder::class,
    SettingSeeder::class,
    ReportSeeder::class,
        VerificationSeeder::class,


]);
    }

    
   

}
