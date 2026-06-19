<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
                'email' => 'admin@itok.com',
                'name' => 'Admin User',
                'role' => 'developer',
                'password' => 'password',
                'subscribe' => 1,
                'verified_at' => date('Y-m-d H:i:s'),
                'email_verified_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
