<?php
namespace Database\Seeders\Tables;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitiesSeeder extends Seeder
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
         * artisan seed:generate --mode=table --tables=activities
         *
         */

        $dataTables = [
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[4,5,6,7,8]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"topic":"Perubahan Wujud Benda","materials":["Es batu","Mangkuk","Matahari atau kipas angin"],"steps":["Ambil es batu dari kulkas","Letakkan di mangkuk","Amati apa yang terjadi setelah 10 menit","Sentuh es batu, apakah berubah?","Ceritakan apa yang terjadi pada es batu"],"facts":["Es batu adalah air yang membeku","Es mencair karena terkena panas","Air bisa berubah jadi es dan es bisa jadi air lagi"],"moral":"Anak belajar tentang perubahan wujud benda melalui pengamatan."}',
                'desc' => 'Eksperimen sederhana tentang perubahan wujud benda.',
                'id' => 104,
                'image' => NULL,
                'moral' => 'Anak belajar tentang perubahan wujud benda melalui pengamatan.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_logis","penasaran"]',
                'slug' => 'es-batu-yang-mencair',
                'sort_order' => 1,
                'status' => 'approved',
                'title' => 'Es Batu yang Mencair',
                'type' => 'ilmu_pengetahuan',
                'views' => 1,
            ],
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[5,6,7,8,9]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"topic":"Pertumbuhan Tumbuhan","materials":["Benih kacang hijau","Tanah","Pot kecil","Air"],"steps":["Isi pot dengan tanah","Tanam benih kacang hijau di dalam tanah","Siram dengan air setiap pagi","Letakkan di tempat yang terkena sinar matahari","Amati setiap hari dan catat perubahannya"],"facts":["Benih membutuhkan air dan sinar matahari untuk tumbuh","Tumbuhan membutuhkan waktu untuk tumbuh besar","Kita harus sabar merawat tumbuhan"],"moral":"Anak belajar sabar dan bertanggung jawab merawat tumbuhan."}',
                'desc' => 'Menanam benih dan mengamati pertumbuhannya.',
                'id' => 105,
                'image' => NULL,
                'moral' => 'Anak belajar sabar dan bertanggung jawab merawat tumbuhan.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["sabar","tanggung_jawab"]',
                'slug' => 'benih-yang-tumbuh',
                'sort_order' => 2,
                'status' => 'approved',
                'title' => 'Benih yang Tumbuh',
                'type' => 'ilmu_pengetahuan',
                'views' => 0,
            ]
        ];

        DB::table("activities")->insert($dataTables);
    }
}