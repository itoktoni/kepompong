<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LatihanOtakSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Ingat dan Sebutkan',
                'desc' => 'Latihan memori dengan mengingat benda-benda.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'memori'],
                'data' => [
                    'type' => 'memori',
                    'exercises' => [
                        ['items' => ['apel', 'pisang', 'anggur'], 'instruction' => 'Ingat 3 buah ini, tutup mata, sebutkan kembali'],
                        ['items' => ['merah', 'biru', 'kuning', 'hijau'], 'instruction' => 'Ingat 4 warna ini, tutup mata, sebutkan kembali'],
                        ['items' => ['kucing', 'anjing', 'burung', 'ikan', 'kelinci'], 'instruction' => 'Ingat 5 hewan ini, tutup mata, sebutkan kembali'],
                    ],
                    'moral' => 'Anak melatih daya ingat dan konsentrasi.',
                ],
            ],
            [
                'title' => 'Mencari yang Berbeda',
                'desc' => 'Anak mencari benda yang berbeda dari kelompoknya.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'perbedaan',
                    'exercises' => [
                        ['group' => '🔴🔴🔴🔵🔴', 'answer' => 'Biru (posisi ke-4)', 'hint' => 'Lihat warna yang beda'],
                        ['group' => '🍎🍎🍐🍎', 'answer' => 'Pear (posisi ke-3)', 'hint' => 'Lihat buah yang beda'],
                        ['group' => '🐱🐱🐱🐶🐱', 'answer' => 'Anjing (posisi ke-4)', 'hint' => 'Lihat hewan yang beda'],
                    ],
                    'moral' => 'Anak melatih ketelitian dan daya observasi.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'latihan_otak',
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
