<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('itqan_admin.initial_admin');

        $email = trim((string) ($admin['email'] ?? ''));
        $password = (string) ($admin['password'] ?? '');
        $name = trim((string) ($admin['name'] ?? '')) ?: 'ITQAN Administrator';

        if ($email === '' || $password === '') {
            throw new RuntimeException(
                'Please set ITQAN_ADMIN_EMAIL and ITQAN_ADMIN_PASSWORD in your .env file before running php artisan migrate --seed.'
            );
        }

        if (strlen($password) < 12) {
            throw new RuntimeException('ITQAN_ADMIN_PASSWORD must be at least 12 characters long.');
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'is_active' => true,
            ]
        );
    }
}
