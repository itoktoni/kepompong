<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MonologSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Hari Pertama di Sekolah',
                'desc' => 'Anak bercerita tentang perasaannya di hari pertama sekolah.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Raka',
                    'emotion' => 'Campuran antara senang dan gugup',
                    'script' => "Hari ini hari pertamaku di sekolah.\nAku sangat gugup, tanganku dingin.\nTapi aku lihat teman-teman baru tersenyum padaku.\nGuru menyapaku dengan ramah.\nTernyata sekolah tidak menakutkan.\nAku senang bisa punya teman baru!",
                    'moral' => 'Anak belajar mengungkapkan perasaan dengan kata-kata.',
                ],
            ],
            [
                'title' => 'Aku Rindu Kakek',
                'desc' => 'Anak bercerita tentang kerinduan pada kakek.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['empati', 'berpikir_kreatif'],
                'data' => [
                    'character' => 'Anak kecil bernama Sari',
                    'emotion' => 'Sedih tapi penuh harapan',
                    'script' => "Kakek tinggal jauh di desa.\nAku rindu cerita-ceritanya sebelum tidur.\nKakek selalu bilang, jangan takut bermimpi besar.\nSuatu hari aku akan naik kereta ke rumah Kakek.\nAku akan peluk Kakek erat-erat.\nSampai saat itu, aku simpan cerita Kakek di hatiku.",
                    'moral' => 'Anak belajar mengungkapkan kerinduan dan kasih sayang.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'monolog',
                    'title' => $item['title'],
                    'desc' => $item['desc'] ?? null,
                    'image' => $item['image'] ?? null,
                    'moral' => $item['data']['moral'] ?? null,
                    'ages' => $item['ages'] ?? [],
                    'agama' => $item['agama'] ?? [],
                    'skills' => $item['skills'] ?? [],
                    'data' => $item['data'] ?? [],
                    'sort_order' => $maxOrder + $i + 1,
                    'active' => true,
                ]
            );
        }
    }
}
