<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $professions = [
            ['كهربائي', 'خدمات الكهرباء', 'تركيب وصيانة التمديدات واللوحات الكهربائية.'],
            ['سباك', 'خدمات المياه', 'إصلاح التسريبات وتمديد شبكات المياه والصرف.'],
            ['نجار', 'الأعمال الخشبية', 'تصنيع وتركيب الأثاث والأبواب والخزائن.'],
            ['دهان', 'التشطيبات والدهان', 'دهان وتشطيب المنازل والمنشآت.'],
            ['حداد', 'الأعمال المعدنية', 'تنفيذ الأبواب والشبابيك والهياكل الحديدية.'],
            ['فني تكييف', 'التكييف والتبريد', 'تركيب وصيانة وتنظيف أجهزة التكييف.'],
        ];

        foreach ($professions as [$name, $categoryName, $description]) {
            $category = Category::where('name', $categoryName)->firstOrFail();
            $profession = Profession::withTrashed()->firstOrNew(['name' => $name]);
            $profession->fill([
                'description' => $description,
                'status' => 'active',
                'category_id' => $category->id,
            ])->save();
            if ($profession->trashed()) {
                $profession->restore();
            }
        }
    }
}
