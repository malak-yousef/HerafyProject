<?php

namespace Database\Seeders;

use App\Models\Profession;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        //
         $profession1 = Profession::create([
    'name' => 'نجار أثاث',
    'description' => 'صناعة وإصلاح الأثاث',
    'status' => true,
    'category_id' => 1
]);
 $profession2 = Profession::create([
    'name' => 'سباك منازل',
    'description' =>  'إصلاح وتمديد شبكات المياه والصرف الصحي',
    'status' => true,
    'category_id' => 2
 ]);
 $profession3 = Profession::create([
    'name' => 'كهربائي منازل',
        'description' => 'تركيب وصيانة الأعطال الكهربائية',
    'status' => true,
    'category_id' => 3
 ]);
 $profession4 = Profession::create([
    'name' => 'دهان',
    'description' => 'أعمال الدهان والتشطيبات الداخلية',
    'status' => true,
    'category_id' => 4
 ]);
 $profession5 = Profession::create([
    'name' => 'فني تكييف',
        'description' => 'تركيب وصيانة أجهزة التكييف',
    'status' => true,
    'category_id' => 5
 ]);
 $profession6 = Profession::create([
     'name' => 'حداد وألمنيوم',
        'description' => 'تصنيع وتركيب الأعمال المعدنية',
    'status' => true,
    'category_id' => 6
 ]);
 
 
    }
}


