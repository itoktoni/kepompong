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
        $this->call(PlanSeeder::class);
        $this->call(WorksheetSeeder::class);
        $this->call(\Database\Seeders\Tables\PilarsSeeder::class);
        $this->call(\Database\Seeders\Tables\SkillsSeeder::class);
        $this->call(\Database\Seeders\Tables\ActivitiesSeeder::class);
        $this->call(\Database\Seeders\Tables\PaymentMethodSeeder::class);
    }
}
