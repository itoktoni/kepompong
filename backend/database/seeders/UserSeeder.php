<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@itok.com'],
            [
                'name' => 'Admin User',
                'role' => 'developer',
                'password' => 'password',
            ]
        );

        User::firstOrCreate(
            ['email' => 'itok@me.com'],
            [
                'name' => 'Itok',
                'role' => 'trial',
                'password' => '111111',
            ]
        );
    }
}
