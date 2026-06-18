<?php
namespace Database\Seeders\Tables;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscribeSeeder extends Seeder
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
         * artisan seed:generate --mode=table --tables=subscribe
         *
         */

        $dataTables = [
            [
                'subscribe_canceled_at' => NULL,
                'subscribe_created_at' => '2026-06-18 19:32:41',
                'subscribe_discount' => 0,
                'subscribe_end_at' => '2027-06-18 19:32:41',
                'subscribe_harga' => 249000,
                'subscribe_id' => 1,
                'subscribe_id_plan' => 3,
                'subscribe_id_user' => 1,
                'subscribe_start_at' => '2026-06-18 19:32:41',
                'subscribe_total' => 249077,
                'subscribe_trial_at' => NULL,
                'subscribe_updated_at' => NULL,
                'subsribe_value' => 5,
            ]
        ];
        
        DB::table("subscribe")->insert($dataTables);
    }
}