<?php

namespace Database\Seeders;

use App\Models\Craftman;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CraftmanSeeder extends Seeder
{
    public function run(): void
    {
        $profession = Profession::first();

        if (! $profession) {
            $this->command->error('لا يوجد Profession في قاعدة البيانات.');

            return;
        }

        // إنشاء بيانات الحرفي
        $craftman = Craftman::create([
            'id_number' => '987654321',
            'profession_id' => $profession->id,
            'experience_years' => 5,
            'description' => 'حرفي تجريبي لاختبار تسجيل الدخول',
            'rating' => 4.50,
            'total_reviews' => 10,
            'hourly_rate' => 50,
            'daily_rate' => 200,
        ]);

        // إنشاء حساب المستخدم وربطه بالحرفي
        User::create([
            'name' => 'Craftman Test',
            'user_name' => 'craftman1',
            'email' => 'craftman1@gmail.com',
            'password' => Hash::make('12345678'),
            'mobile' => '0599000000',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Gaza',
            'actor_type' => Craftman::class,
            'actor_id' => $craftman->id,
        ]);
        User::create([
            'name' => 'Craftman Test2',
            'user_name' => 'craftman2',
            'email' => 'craftman2@gmail.com',
            'password' => Hash::make('123456788'),
            'mobile' => '0599000000',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Gaza',
            'actor_type' => Craftman::class,
            'actor_id' => $craftman->id,
        ]);
        User::create([
            'name' => 'Craftman Test3',
            'user_name' => 'craftman3',
            'email' => 'craftman3@gmail.com',
            'password' => Hash::make('123456789'),
            'mobile' => '0599000000',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Gaza',
            'actor_type' => Craftman::class,
            'actor_id' => $craftman->id,
        ]);
    }
}
