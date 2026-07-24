<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'user_name' => 'admin',
            'email' => 'admin1@example.com',
            'password' => Hash::make('123123'),
            'mobile' => '0599999999',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Gaza',
            'date' => now(),
        ]);
    }
}
