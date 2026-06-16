<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(DiscountSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(PlanSeeder::class);
        $this->call(PilarSeeder::class);
        $this->call(SkillSeeder::class);
        $this->call(WorksheetSeeder::class);
        $this->call(PlanTableSeeder::class);
        $this->call(\Database\Seeders\Tables\PlanSeeder::class);
        $this->call(\Database\Seeders\Tables\PilarsSeeder::class);
        $this->call(\Database\Seeders\Tables\SkillsSeeder::class);
    }
}
