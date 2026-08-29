<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * Get base Firebase Realtime Database URL.
     */
    protected static function getDbUrl(): string
    {
        $url = config('services.firebase.database_url', 'https://pharmacymanagesystem-default-rtdb.firebaseio.com');
        return rtrim($url, '/');
    }

    /**
     * Save/Update a user in Firebase Realtime Database.
     */
    public static function syncUser(User $user): bool
    {
        try {
            $url = self::getDbUrl() . "/users/" . md5($user->email) . ".json";
            
            $payload = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'admin',
                'password' => $user->password,
                'created_at' => $user->created_at?->toIso8601String() ?? now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ];

            $response = Http::timeout(3)->put($url, $payload);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Firebase syncUser error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user from Firebase.
     */
    public static function deleteUser(string $email): bool
    {
        try {
            $url = self::getDbUrl() . "/users/" . md5($email) . ".json";
            $response = Http::timeout(3)->delete($url);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Firebase deleteUser error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all users from Firebase Realtime Database.
     */
    public static function getUsersFromFirebase(): array
    {
        try {
            $url = self::getDbUrl() . "/users.json";
            $response = Http::timeout(3)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : [];
            }
        } catch (\Throwable $e) {
            Log::error('Firebase getUsersFromFirebase error: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * Sync Firebase Users into local database if missing.
     */
    public static function syncFirebaseUsersToLocal(): void
    {
        try {
            $fbUsers = self::getUsersFromFirebase();
            if (empty($fbUsers)) {
                return;
            }

            foreach ($fbUsers as $key => $fbData) {
                if (empty($fbData['email'])) continue;

                $existingUser = User::withTrashed()->where('email', $fbData['email'])->first();
                if ($existingUser) {
                    continue; // Preserve local user data as primary
                }

                $user = new User();
                $user->name = $fbData['name'] ?? 'Pharmacy User';
                $user->email = $fbData['email'];
                $user->role = $fbData['role'] ?? 'admin';
                $user->password = !empty($fbData['password']) ? $fbData['password'] : 'admin123';
                $user->save();
            }
        } catch (\Throwable $e) {
            Log::error('Firebase syncFirebaseUsersToLocal error: ' . $e->getMessage());
        }
    }

    /**
     * Ensure local database is seeded with default users.
     */
    public static function ensureDatabaseSeeded(): void
    {
        try {
            if (User::withTrashed()->count() === 0) {
                $admin = new User();
                $admin->name = 'Muhammad Faizan Khan Lodhi';
                $admin->email = 'admin@pharmacy.com';
                $admin->role = 'admin';
                $admin->password = 'admin123';
                $admin->save();
            }
        } catch (\Throwable $e) {
            Log::error('ensureDatabaseSeeded error: ' . $e->getMessage());
        }
    }
}
