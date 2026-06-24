<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OutdoorSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Berburu Harta Karun Alam',
                'desc' => 'Anak mencari benda-benda alam di sekitar rumah.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_kreatif', 'kerjasama'],
                'data' => [
                    'location' => 'Taman atau halaman rumah',
                    'checklist' => [
                        'Daun berbentuk hati',
                        'Batu kecil yang halus',
                        'Bunga yang harum',
                        'Ranting bercabang',
                        'Serangga yang merayap',
                    ],
                    'steps' => [
                        'Cari semua benda di checklist',
                        'Amati bentuk dan warna setiap benda',
                        'Ceritakan temuanmu kepada teman',
                        'Kembalikan benda ke tempatnya',
                    ],
                    'moral' => 'Anak belajar mengamati alam dan menjaga lingkungan.',
                ],
            ],
            [
                'title' => 'Mengamati Awan',
                'desc' => 'Anak mengamati bentuk awan di langit.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'bersyukur'],
                'data' => [
                    'location' => 'Taman atau lapangan terbuka',
                    'checklist' => [],
                    'steps' => [
                        'Berbaring di rumput dan pandang langit',
                        'Cari awan yang bentuknya menarik',
                        'Ceritakan awan itu mirip apa',
                        'Hitung berapa awan yang bisa kamu lihat',
                    ],
                    'moral' => 'Anak belajar mengamati dan berimajinasi.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'outdoor',
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
