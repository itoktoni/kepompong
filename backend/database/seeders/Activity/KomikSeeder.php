<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KomikSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Kucing dan Kupu-Kupu',
                'desc' => 'Komik tentang kucing yang mengejar kupu-kupu dan belajar sabar.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['sabar', 'berpikir_kreatif'],
                'data' => [
                    'pages' => [
                        ['num' => 1, 'text' => 'Di kebun bunga, ada kucing kecil bernama Mimi. Mimi suka bermain di antara bunga-bunga.'],
                        ['num' => 2, 'text' => 'Suatu hari, Mimi melihat kupu-kupu cantik. Sayapnya berwarna jingga dan biru.'],
                        ['num' => 3, 'text' => 'Mimi mengejar kupu-kupu itu. Lompat sana, lompat sini. Tapi kupu-kupu terlalu cepat!'],
                        ['num' => 4, 'text' => 'Mimi jatuh ke dalam kolam! Basah semua bulunya. Kupu-kupu tertawa kecil.'],
                        ['num' => 5, 'text' => 'Mimi sedih. Ia duduk diam di bawah pohon. Tiba-tiba kupu-kupu hinggap di hidungnya.'],
                        ['num' => 6, 'text' => 'Ternyata kupu-kupu mau berteman! Mimi tersenyum. Kadang kita tidak perlu mengejar, cukup diam dan menunggu.'],
                    ],
                    'moral' => 'Anak belajar sabar dan tidak memaksakan kehendak.',
                ],
            ],
            [
                'title' => 'Semut dan Belalang',
                'desc' => 'Komik tentang semut rajin dan belalang yang suka bermain.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['tanggung_jawab', 'disiplin'],
                'data' => [
                    'pages' => [
                        ['num' => 1, 'text' => 'Semut kecil bekerja keras setiap hari. Ia mengumpulkan makanan untuk musim hujan.'],
                        ['num' => 2, 'text' => 'Belalang hanya bernyanyi dan bermain. Mengapa kau bekerja terus? katanya pada semut.'],
                        ['num' => 3, 'text' => 'Semut berkata: Musim hujan akan datang. Kita harus siap. Tapi belalang tertawa.'],
                        ['num' => 4, 'text' => 'Hujan datang! Belalang kedinginan dan kelaparan. Ia mengetuk rumah semut.'],
                        ['num' => 5, 'text' => 'Semut membuka pintu. Masuklah, kawan! Ada makanan untukmu.'],
                        ['num' => 6, 'text' => 'Belalang belajar. Ia ikut semut mengumpulkan makanan. Bersama lebih mudah!'],
                    ],
                    'moral' => 'Anak belajar pentingnya kerja keras dan persiapan.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'komik',
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
