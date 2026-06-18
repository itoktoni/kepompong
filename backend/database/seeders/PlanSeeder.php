<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Subscribe;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::truncate();
        Subscribe::truncate();

         $plan = [
            // [
            //     'plan_nama' => 'Free Trial 3 Hari',
            //     'plan_status' => 1,
            //     'plan_keterangan' => '1 Anak Free untuk 3 hari',
            //     'plan_value' => 1,
            //     'plan_harga' => 0,
            //     'plan_fee' => 0,
            //     'plan_periode' => '3d',
            //     'plan_color' => 'rgb(178, 190, 181)',
            //     'plan_interval' => '3d',
            //     'plan_recomended' => 0,
            // ],
            [
                'plan_nama' => '🥉 Paket Basic - 1 Bulan',
                'plan_status' => 1,
                'plan_keterangan' => 'All feature 1 anak',
                'plan_value' => 1,
                'plan_harga' => 49000,
                'plan_coret' => 50000,
                'plan_fee' => 0,
                'plan_periode' => '1m',
                'plan_color' => 'rgb(178, 190, 181)',
                'plan_interval' => '1m',
                'plan_recomended' => 0,
            ],
            [
                'plan_nama' => '🥈 Paket Fokus (3 Bulan)',
                'plan_status' => 1,
                'plan_keterangan' => 'All feature max 2 anak',
                'plan_value' => 2,
                'plan_harga' => 129000,
                'plan_coret' => 147000,
                'plan_fee' => 0,
                'plan_periode' => '3m',
                'plan_color' => 'rgb(109, 190, 123)',
                'plan_interval' => '3m',
                'plan_recomended' => 0,
            ],
            // [
            //     'plan_nama' => '6 Bulan',
            //     'plan_status' => 1,
            //     'plan_keterangan' => 'Semua feature untuk 5 anak, Rp 249.000 (Diskon Early Bird) 100 orang pertama di bulan ini',
            //     'plan_value' => 5,
            //     'plan_harga' => 249000,
            //     'plan_coret' => 516000,
            //     'plan_fee' => 0,
            //     'plan_periode' => '6m',
            //     'plan_color' => 'rgb(33, 150, 243)',
            //     'plan_interval' => '6m',
            //     'plan_recomended' => 1,
            // ],
            [
                'plan_nama' => '🥇 Paket Juara (12 Bulan)',
                'plan_status' => 1,
                'plan_keterangan' => 'All feature Max 5 anak, (Diskon Early Bird) 100 orang pertama di bulan ini',
                'plan_value' => 5,
                'plan_harga' => 249000,
                'plan_coret' => 516000,
                'plan_fee' => 0,
                'plan_periode' => '1y',
                'plan_color' => 'rgb(33, 150, 243)',
                'plan_interval' => '1y',
                'plan_recomended' => 1,
            ],
        ];

        Plan::insert($plan);

    }
}
