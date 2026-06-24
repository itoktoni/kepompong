<?php

namespace Database\Seeders\Tables;

use Illuminate\Database\Seeder;

class ActivitiesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(\Database\Seeders\Activity\StorytellingSeeder::class);
        $this->call(\Database\Seeders\Activity\MusikGerakSeeder::class);
        $this->call(\Database\Seeders\Activity\BermainPeranSeeder::class);
        $this->call(\Database\Seeders\Activity\PermainanSeeder::class);
        $this->call(\Database\Seeders\Activity\MonologSeeder::class);
        $this->call(\Database\Seeders\Activity\ProyekKreatifSeeder::class);
        $this->call(\Database\Seeders\Activity\PuzzleSeeder::class);
        $this->call(\Database\Seeders\Activity\MindfulnessSeeder::class);
        $this->call(\Database\Seeders\Activity\OutdoorSeeder::class);
        $this->call(\Database\Seeders\Activity\IlmuPengetahuanSeeder::class);
        $this->call(\Database\Seeders\Activity\TebakTeakanSeeder::class);
        $this->call(\Database\Seeders\Activity\PermainanTanganSeeder::class);
        $this->call(\Database\Seeders\Activity\LatihanOtakSeeder::class);
        $this->call(\Database\Seeders\Activity\KomikSeeder::class);
        $this->call(\Database\Seeders\Activity\WorksheetActivitySeeder::class);
        $this->call(\Database\Seeders\Activity\ColoringSeeder::class);
        $this->call(\Database\Seeders\Activity\MengenalBendaSeeder::class);
    }
}
