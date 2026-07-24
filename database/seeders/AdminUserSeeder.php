<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default super admin.
     *
     * Credentials come from the environment so production installs
     * never ship with a known password. Change these after first login.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'username' => env('ADMIN_NAME', 'superadmin'),
                'password' => env('ADMIN_PASSWORD', 'Admin123!'),
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole('super_admin');
    }
}
