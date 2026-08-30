<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure Admin Users exist
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@pharmacy.com'],
            [
                'name' => 'Muhammad Faizan Khan Lodhi',
                'role' => 'admin',
                'password' => 'admin123',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => 'admin123',
            ]
        );

        $this->call([
            DemoDataSeeder::class,
            MigrateExistingMedicinesPackagingSeeder::class,
        ]);
    }
}




