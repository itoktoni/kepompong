<?php
namespace Database\Seeders\Tables;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Command :
         * artisan seed:generate --table-mode --tables=plan
         *
         */

        $dataTables = [
            [
                'plan_color' => 'rgb(178, 190, 181]',
                'plan_coret' => 50000,
                'plan_fee' => 0,
                'plan_harga' => 49000,
                'plan_id' => 1,
                'plan_interval' => '1m',
                'plan_keterangan' => 'All feature 1 anak',
                'plan_nama' => '🥉 Paket Basic - 1 Bulan',
                'plan_periode' => '1m',
                'plan_recomended' => 0,
                'plan_status' => 1,
                'plan_value' => 1,
            ],
            [
                'plan_color' => 'rgb(109, 190, 123]',
                'plan_coret' => 147000,
                'plan_fee' => 0,
                'plan_harga' => 129000,
                'plan_id' => 2,
                'plan_interval' => '3m',
                'plan_keterangan' => 'All feature max 2 anak',
                'plan_nama' => '🥈 Paket Fokus (3 Bulan]',
                'plan_periode' => '3m',
                'plan_recomended' => 0,
                'plan_status' => 1,
                'plan_value' => 2,
            ],
            [
                'plan_color' => 'rgb(33, 150, 243]',
                'plan_coret' => 516000,
                'plan_fee' => 0,
                'plan_harga' => 249000,
                'plan_id' => 3,
                'plan_interval' => '1y',
                'plan_keterangan' => 'All feature Max 5 anak, (Diskon Early Bird] 100 orang pertama di bulan ini',
                'plan_nama' => '🥇 Paket Juara (12 Bulan]',
                'plan_periode' => '1y',
                'plan_recomended' => 1,
                'plan_status' => 1,
                'plan_value' => 5,
            ]
        ];
        
        DB::table("plan")->insert($dataTables);
    }
}