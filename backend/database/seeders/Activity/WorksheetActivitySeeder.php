<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorksheetActivitySeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Mengenal Huruf A-B-C',
                'desc' => 'Worksheet mengenal dan menulis huruf A, B, C.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'literasi'],
                'data' => [
                    'topic' => 'Huruf dan Abjad',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus huruf A'],
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus huruf B'],
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus huruf C'],
                        ['type' => 'match', 'content' => 'Cocokkan huruf besar dengan huruf kecil'],
                    ],
                    'moral' => 'Anak belajar mengenal dan menulis huruf dasar.',
                ],
            ],
            [
                'title' => 'Berhitung 1-5',
                'desc' => 'Worksheet berhitung angka 1 sampai 5.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'matematika'],
                'data' => [
                    'topic' => 'Angka dan Berhitung',
                    'exercises' => [
                        ['type' => 'count', 'content' => 'Hitung jumlah apel dan tulis angkanya'],
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus angka 1-5'],
                        ['type' => 'circle', 'content' => 'Lingkari angka yang lebih besar'],
                        ['type' => 'draw', 'content' => 'Gambar bintang sesuai jumlah yang diminta'],
                    ],
                    'moral' => 'Anak belajar berhitung dan mengenal angka.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'worksheet',
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
