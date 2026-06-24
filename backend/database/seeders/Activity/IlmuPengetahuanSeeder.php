<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IlmuPengetahuanSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Es Batu yang Mencair',
                'desc' => 'Eksperimen sederhana tentang perubahan wujud benda.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'penasaran'],
                'data' => [
                    'topic' => 'Perubahan Wujud Benda',
                    'materials' => ['Es batu', 'Mangkuk', 'Matahari atau kipas angin'],
                    'steps' => [
                        'Ambil es batu dari kulkas',
                        'Letakkan di mangkuk',
                        'Amati apa yang terjadi setelah 10 menit',
                        'Sentuh es batu, apakah berubah?',
                        'Ceritakan apa yang terjadi pada es batu',
                    ],
                    'facts' => ['Es batu adalah air yang membeku', 'Es mencair karena terkena panas', 'Air bisa berubah jadi es dan es bisa jadi air lagi'],
                    'moral' => 'Anak belajar tentang perubahan wujud benda melalui pengamatan.',
                ],
            ],
            [
                'title' => 'Benih yang Tumbuh',
                'desc' => 'Menanam benih dan mengamati pertumbuhannya.',
                'ages' => [5, 6, 7, 8, 9],
                'skills' => ['sabar', 'tanggung_jawab'],
                'data' => [
                    'topic' => 'Pertumbuhan Tumbuhan',
                    'materials' => ['Benih kacang hijau', 'Tanah', 'Pot kecil', 'Air'],
                    'steps' => [
                        'Isi pot dengan tanah',
                        'Tanam benih kacang hijau di dalam tanah',
                        'Siram dengan air setiap pagi',
                        'Letakkan di tempat yang terkena sinar matahari',
                        'Amati setiap hari dan catat perubahannya',
                    ],
                    'facts' => ['Benih membutuhkan air dan sinar matahari untuk tumbuh', 'Tumbuhan membutuhkan waktu untuk tumbuh besar', 'Kita harus sabar merawat tumbuhan'],
                    'moral' => 'Anak belajar sabar dan bertanggung jawab merawat tumbuhan.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'ilmu_pengetahuan',
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
