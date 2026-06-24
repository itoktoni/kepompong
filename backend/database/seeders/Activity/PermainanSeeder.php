<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermainanSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Ular Tangga Ajaib',
                'desc' => 'Permainan ular tangga sederhana untuk anak.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'sabar'],
                'data' => [
                    'players' => '2-4 anak',
                    'materials' => ['Papan ular tangga', 'Dadu', 'Pion warna-warni'],
                    'rules' => [
                        'Lempar dadu dan maju sesuai angka',
                        'Naik tanggi jika mendarat di tangga',
                        'Turun ke bawah jika mendarat di kepala ular',
                        'Siapa duluan sampai finish, dia menang',
                    ],
                    'moral' => 'Anak belajar sabar, sportif, dan berhitung.',
                ],
            ],
            [
                'title' => 'Petak Umpet',
                'desc' => 'Permainan tradisional petak umpet.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_kreatif', 'kerjasama'],
                'data' => [
                    'players' => '3-8 anak',
                    'materials' => [],
                    'rules' => [
                        'Satu anak menjaga dan menghitung',
                        'Anak lain bersembunyi',
                        'Penjaga mencari yang bersembunyi',
                        'Yang ditemukan duluan jadi penjaga berikutnya',
                    ],
                    'moral' => 'Anak belajar strategi dan bersosialisasi.',
                ],
            ],
            [
                'title' => 'Congklak',
                'desc' => 'Permainan tradisional congklak Indonesia.',
                'ages' => [5, 6, 7, 8, 9],
                'skills' => ['berpikir_kreatif', 'strategi'],
                'data' => [
                    'players' => '2 anak',
                    'materials' => ['Papan congklak', 'Biji congklak'],
                    'rules' => [
                        'Ambil semua biji dari satu lubang',
                        'Bagikan satu per satu ke lubang berikutnya',
                        'Jika berhenti di lubang kosong, ambil biji lawan',
                        'Paling banyak biji di rumah, dia menang',
                    ],
                    'moral' => 'Anak belajar berhitung dan berpikir strategis.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'permainan',
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
