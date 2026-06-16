<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@itok.com'],
            [
                'name' => 'Admin User',
                'role' => 'developer',
                'password' => Hash::make('password'),
            ]
        );

        User::create([
            'name' => 'Itok',
            'email' => 'itok@me.com',
            'role' => 'trial',
            'password' => Hash::make('111111'),
        ]);
    }
}
