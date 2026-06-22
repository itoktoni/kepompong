<?php
namespace Database\Seeders\Tables;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnakSeeder extends Seeder
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
         * artisan seed:generate --mode=table --tables=anak
         *
         */

        $dataTables = [
            [
                'anak_agama' => 'islam',
                'anak_avatar' => NULL,
                'anak_bulan_lahir' => 9,
                'anak_created_at' => '2026-06-22 14:54:09',
                'anak_emoji' => '👶',
                'anak_gender' => 'male',
                'anak_id' => 1,
                'anak_id_user' => 1,
                'anak_nama' => 'Test',
                'anak_settings' => NULL,
                'anak_tahun_lahir' => 2019,
                'anak_tanggal_lahir' => 16,
                'anak_umur' => 7,
                'anak_updated_at' => NULL,
            ]
        ];
        
        DB::table("anak")->insert($dataTables);
    }
}