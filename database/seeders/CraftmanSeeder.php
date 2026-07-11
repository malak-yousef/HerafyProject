<?php

namespace Database\Seeders;

use App\Models\Craftman;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CraftmanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Craftman::factory(5)->create();
        
// طريقة تانية لانشاء سيدر بتعمل استدعاء بنفس الطريقة 
        $craftman1 =Craftman::create([
            'name' =>'John',
            'craft_name' => 'Craftman 1',
            'email' => 'H9A7o@example.com',
            'password'=> bcrypt('password'),
            'experience_years'=> 5,
            'description'=> 'Craftman 1 description',
            'rating'=> 4.5,
            'total_reviews'=> 100,
            'is_active'=> true,  
            'profile_image'=> 'craftman1.jpg', 
        ]);
        $craftman2 =Craftman::create([
            'name' =>'Ali',
            'craft_name' => 'Craftman 2',
            'email' => 'Httttt7o@example.com',
            'password'=> bcrypt('password'),
            'experience_years'=> 5,
            'description'=> 'Craftman 2 description',
            'rating'=> 4.5,
            'total_reviews'=> 100,
            'is_active'=> true,    
            'profile_image'=> 'craftman2.jpg',
        ]);
        $craftman3 =Craftman::create([
            'name' =>'Ahmed',
            'craft_name' => 'Craftman 3',
            'email' => 'ssss7o@example.com',
            'password'=> bcrypt('password'),
            'experience_years'=> 7,
            'description'=> 'Craftman 3 description',
            'rating'=> 2.5,
            'total_reviews'=> 20,
            'is_active'=> true,  
            'profile_image'=> 'craftman3.jpg',  
        ]);

        
    }
}