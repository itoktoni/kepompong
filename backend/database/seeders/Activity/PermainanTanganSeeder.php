<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermainanTanganSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Tepuk Tangan Bilangan',
                'desc' => 'Permainan tepuk tangan sambil berhitung.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_logis', 'motorik_halus'],
                'data' => [
                    'moves' => [
                        'Tepuk tangan 1 kali sambil bilang SATU',
                        'Tepuk tangan 2 kali sambil bilang DUA',
                        'Tepuk tangan 3 kali sambil bilang TIGA',
                        'Lanjutkan sampai angka 10',
                        'Ulangi dengan tempo yang lebih cepat',
                    ],
                    'moral' => 'Anak belajar berhitung sambil melatih koordinasi tangan.',
                ],
            ],
            [
                'title' => 'Gunting Batu Kertas',
                'desc' => 'Permainan tangan klasik gunting batu kertas.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'sportif'],
                'data' => [
                    'moves' => [
                        'Buat tangan menjadi kepalan',
                        'Hitung bersama: satu, dua, tiga!',
                        'Pilih: gunting (jari telunjuk dan tengah), batu (kepalan), atau kertas (telapak terbuka)',
                        'Gunting kalahkan kertas, kertas kalahkan batu, batu kalahkan gunting',
                        'Siapa menang, dia dapat satu poin',
                    ],
                    'moral' => 'Anak belajar sportif dan menerima kekalahan.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'permainan_tangan',
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
