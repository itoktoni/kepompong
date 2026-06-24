<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MindfulnessSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Pernapasan Balon',
                'desc' => 'Latihan pernapasan sederhana dengan bayangan mengisi balon.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['konsentrasi', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk dengan nyaman dan tutup mata',
                        'Bayangkan perutmu adalah balon kosong',
                        'Tarik napas pelan lewat hidung, balon mengembang',
                        'Hembuskan napas pelan lewat mulut, balon mengempis',
                        'Ulangi 5 kali sambil tersenyum',
                    ],
                    'moral' => 'Anak belajar menenangkan diri dengan pernapasan.',
                ],
            ],
            [
                'title' => 'Mendengarkan Suara Alam',
                'desc' => 'Latihan mendengarkan dengan tenang suara di sekitar.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'bersyukur'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk di tempat yang nyaman',
                        'Tutup mata dan dengarkan suara di sekitarmu',
                        'Hitung berapa suara yang bisa kamu dengar',
                        'Bayangkan suara itu datang dari mana',
                        'Buka mata dan ceritakan apa yang kamu dengar',
                    ],
                    'moral' => 'Anak belajar fokus dan menghargai lingkungan sekitar.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'mindfulness',
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
