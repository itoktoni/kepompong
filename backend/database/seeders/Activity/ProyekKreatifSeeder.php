<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProyekKreatifSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Origami Kupu-Kupu',
                'desc' => 'Membuat kupu-kupu dari kertas lipat origami.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_kreatif', 'sabar'],
                'data' => [
                    'materials' => ['Kertas origami warna-warni', 'Pensil', 'Gunting'],
                    'steps' => [
                        'Lipat kertas menjadi segitiga',
                        'Buka lipatan dan lipat sisi kiri ke tengah',
                        'Lipat sisi kanan ke tengah',
                        'Balik kertas dan lipat ujung atas ke bawah',
                        'Lipat sayap ke atas dan ke bawah',
                        'Gambar pola sayap dengan pensil',
                    ],
                    'result' => 'Kupu-kupu cantik dari kertas origami',
                    'moral' => 'Anak belajar sabar dan kreatif dalam membuat kerajinan.',
                ],
            ],
            [
                'title' => 'Kolase Pelangi',
                'desc' => 'Membuat kolase pelangi dari potongan kertas warna.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'materials' => ['Kertas karton', 'Kertas warna', 'Gunting', 'Lem'],
                    'steps' => [
                        'Potong kertas warna menjadi kecil-kecil',
                        'Gambar lengkungan pelangi di karton',
                        'Tempel potongan kertas sesuai warna pelangi',
                        'Tambahkan awan kapas di sisi pelangi',
                    ],
                    'result' => 'Kolase pelangi warna-warni',
                    'moral' => 'Anak belajar mengenal warna dan melatih motorik halus.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'proyek_kreatif',
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
