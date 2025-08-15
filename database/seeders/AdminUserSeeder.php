<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // change these or use env() (see below)
        $email = env('ADMIN_EMAIL', 'info@ghanainsider.com');
        $password = env('ADMIN_PASSWORD', 'ChangeMe123!');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'GI-DE Admin',
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
