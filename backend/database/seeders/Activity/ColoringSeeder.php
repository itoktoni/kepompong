<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ColoringSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Mewarnai Kupu-Kupu',
                'desc' => 'Mewarnai gambar kupu-kupu dengan krayon.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Kupu-kupu',
                    'suggested_colors' => ['Jingga', 'Biru', 'Kuning', 'Hijau'],
                    'steps' => [
                        'Pilih krayon warna yang kamu suka',
                        'Warnai sayap kupu-kupu dengan warna cerah',
                        'Warnai badan kupu-kupu dengan warna gelap',
                        'Tambahkan pola titik-titik di sayap',
                        'Warnai latar belakang dengan warna langit atau rumput',
                    ],
                    'moral' => 'Anak belajar mengenal warna dan melatih kreativitas.',
                ],
            ],
            [
                'title' => 'Mewarnai Pelangi',
                'desc' => 'Mewarnai gambar pelangi dengan tujuh warna.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Pelangi',
                    'suggested_colors' => ['Merah', 'Jingga', 'Kuning', 'Hijau', 'Biru', 'Nila', 'Ungu'],
                    'steps' => [
                        'Warnai lengkungan terluar dengan warna merah',
                        'Warnai lengkungan kedua dengan warna jingga',
                        'Lanjutkan dengan kuning, hijau, biru, nila, ungu',
                        'Warnai awan dengan warna putih atau abu-abu muda',
                        'Warnai matahari di sisi pelangi dengan warna kuning',
                    ],
                    'moral' => 'Anak belajar mengenal urutan warna pelangi.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'coloring',
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
