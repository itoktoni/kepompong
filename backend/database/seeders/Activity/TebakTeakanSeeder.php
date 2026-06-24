<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TebakTeakanSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Tebak Hewan dari Suara',
                'desc' => 'Anak menebak hewan dari suara yang didengar.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'questions' => [
                        ['sound' => 'Moo moo', 'answer' => 'Sapi', 'hint' => 'Hewan yang memberi susu'],
                        ['sound' => 'Kukuruyuk', 'answer' => 'Ayam', 'hint' => 'Hewan yang bertelur'],
                        ['sound' => 'Meong', 'answer' => 'Kucing', 'hint' => 'Hewan yang suka menangkap tikus'],
                        ['sound' => 'Guk guk', 'answer' => 'Anjing', 'hint' => 'Hewan yang setia pada tuannya'],
                    ],
                    'moral' => 'Anak belajar mengenal suara hewan dan melatih daya ingat.',
                ],
            ],
            [
                'title' => 'Tebak Buah dari Deskripsi',
                'desc' => 'Anak menebak buah dari ciri-ciri yang disebutkan.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'pengetahuan'],
                'data' => [
                    'questions' => [
                        ['description' => 'Buah ini kuning, panjang, dan manis', 'answer' => 'Pisang', 'hint' => 'Monyet suka makan ini'],
                        ['description' => 'Buah ini bulat, merah, dan renyah', 'answer' => 'Apel', 'hint' => 'Buah dari luar negeri yang sangat populer'],
                        ['description' => 'Buah ini besar, hijau di luar, merah di dalam', 'answer' => 'Semangka', 'hint' => 'Buah yang banyak airnya'],
                        ['description' => 'Buah ini kecil, ungu, dan asam manis', 'answer' => 'Anggur', 'hint' => 'Buah yang dibuat jus atau kismis'],
                    ],
                    'moral' => 'Anak belajar mengenal buah-buahan dan ciri-cirinya.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'tebak_tebakan',
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
