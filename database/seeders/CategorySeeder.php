<?php

namespace Database\Seeders;

use App\Models\Category;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     $category1 = Category::create([
    'name' => 'النجارة',
    'description' => 'خدمات صناعة وإصلاح الأثاث والأبواب والنوافذ الخشبية'
]);

$category2 = Category::create([
    'name' => 'السباكة',
    'description' => 'خدمات تمديدات المياه وإصلاح الأعطال الصحية'
]);

$category3 = Category::create([
    'name' => 'الكهرباء',
    'description' => 'خدمات التمديدات الكهربائية وصيانة الأعطال'
]);

$category4 = Category::create([
    'name' => 'الدهان والديكور',
    'description' => 'أعمال الدهان والتشطيبات والديكورات الداخلية'
]);

$category5 = Category::create([
    'name' => 'التكييف والتبريد',
    'description' => 'تركيب وصيانة أجهزة التكييف والتبريد'
]);

$category6 = Category::create([
    'name' => 'الحدادة والألمنيوم',
    'description' => 'تصنيع وتركيب الأبواب والنوافذ والهياكل المعدنية'
]);
    }
}
