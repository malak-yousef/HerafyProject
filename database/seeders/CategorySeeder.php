<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'خدمات الكهرباء' => 'تركيب وصيانة الأنظمة والتمديدات الكهربائية.',
            'خدمات المياه' => 'تمديد وصيانة شبكات المياه والصرف الصحي.',
            'الأعمال الخشبية' => 'تصنيع وتركيب وصيانة الأثاث والأبواب الخشبية.',
            'التشطيبات والدهان' => 'أعمال الدهان والتشطيبات الداخلية والخارجية.',
            'الأعمال المعدنية' => 'تصنيع وتركيب وصيانة الأعمال الحديدية.',
            'التكييف والتبريد' => 'تركيب وصيانة أجهزة التكييف والتبريد.',
        ] as $name => $description) {
            $category = Category::withTrashed()->firstOrNew(['name' => $name]);
            $category->description = $description;
            $category->save();
            if ($category->trashed()) {
                $category->restore();
            }
        }
    }
}
