<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tinatawag nito ang hiwalay na seeder files
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
        ]);
    }
}