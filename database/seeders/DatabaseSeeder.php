<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure Default Admin Users exist if not already present
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@pharmacy.com'],
            [
                'name' => 'Muhammad Faizan Khan Lodhi',
                'role' => 'admin',
                'password' => 'admin123',
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => 'admin123',
            ]
        );

        // 2. Seed Clean Master Categories & Units (NO demo medicines or transactions)
        $this->call([
            DemoDataSeeder::class,
        ]);

        // 3. Restore all users saved in cloud
        try {
            \App\Services\FirebaseService::syncFirebaseUsersToLocal();
        } catch (\Throwable $e) {
            // ignore network issues during local/build
        }
    }
}
