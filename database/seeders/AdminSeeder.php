<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Zyrel Clide',
            'last_name' => 'Amiana',
            'email' => 'adminclide@gmail.com',
            'password' => Hash::make('adminclide'),
            'role' => 'admin',
            'contact_number' => '09770813692', // Ready for PhilSMS if needed
        ]);
    }
}