<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin
        User::firstOrCreate(
            ['email' => 'adminclide@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Clide',
                'password' => Hash::make('adminclide'),
                'role' => 'admin',
            ]
        );

        // 2. Create Default Teacher
        User::firstOrCreate(
            ['email' => 'erroljohnlubgoban@gmail.com'],
            [
                'first_name' => 'Errol John',
                'last_name' => 'Lubgoban',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
            ]
        );

        // 3. Create Default Student
        User::firstOrCreate(
            ['email' => 'zyrelclideamiana@gmail.com'],
            [
                'first_name' => 'Zyrel Clide',
                'last_name' => 'Amiana',
                'password' => Hash::make('@Clide01'),
                'role' => 'student',
            ]
        );
    }
}