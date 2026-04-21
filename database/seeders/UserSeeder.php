<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Admin Account
        User::create([
            'name' => 'ADONIS ADMIN',
            'email' => 'admin@dev.ph',
            'is_admin' => 1,
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        // 2. Create Standard User Account
        User::create([
            'name' => 'STUDIO USER',
            'email' => 'user@dev.ph',
            'is_admin' => 0,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
        ]);
    }
}