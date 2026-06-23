<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MusikGerakSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Berwudhu',
                'desc' => 'Lagu anak berwudhu.',
                'agama' => '["islam"]',
                'ages' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                'skills' => ['berpikir_kreatif', 'rutin_belajar'],
                'data' => [
                    'audio_url' => 'https://audius.co/embed/track/tonijambo/berwudhu?flavor=compact',
                    'lyrics' => "🎈 Cuci dua tangan,\nlalu kumurlah,\nbersihkan hidung,\nlalu mukamu,\ncuci tangan kanan,\ncuci tangan kiri,\nbasuhkan rambut, telinga,\njuga kaki",
                    'moves' => ['Cuci dua tangan', 'Masukan air ke hidung', 'Basuh muka dengan air', 'Basuh tangan kanan', 'Basuh tangan kiri', 'Basuh rambut dan telinga', 'Basuh kaki'],
                    'moral' => 'Lagu ini mengajarkan anak berwudhu.',
                ],
            ],
            [
                'title' => 'Lagu Anak Indonesia 60 Menit',
                'desc' => 'Kumpulan lagu anak Indonesia selama 60 menit untuk menemani belajar dan bermain.',
                'ages' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                'skills' => ['berpikir_kreatif', 'rutin_belajar'],
                'data' => [
                    'audio_url' => 'https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/soundcloud%253Atracks%253A346703008&color=%23ff5500&auto_play=false&hide_related=true&show_comments=false&show_user=true&show_reposts=false&show_teaser=false',
                    'moves' => ['Dengarkan musik sambil bermain', 'Bertepuk tangan mengikuti irama', 'Bernyanyi bersama lagu yang dikenal', 'Menari bebas sesuai nada'],
                    'moral' => 'Lagu anak Indonesia mengajarkan nilai-nilai baik dengan cara yang menyenangkan.',
                ],
            ],
            [
                'title' => 'Lagu Anak YouTube',
                'desc' => 'Kumpulan lagu anak Indonesia populer dari YouTube.',
                'ages' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                'skills' => ['berpikir_kreatif', 'rutin_belajar'],
                'data' => [
                    'audio_url' => 'https://www.youtube.com/embed/OSFOmU38jZ4',
                    'moves' => ['Dengarkan musik sambil bermain', 'Bertepuk tangan mengikuti irama', 'Bernyanyi bersama lagu yang dikenal', 'Menari bebas sesuai nada'],
                    'moral' => 'Lagu anak Indonesia mengajarkan nilai-nilai baik dengan cara yang menyenangkan.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::create([
                'type' => 'musik_gerak',
                'title' => $item['title'],
                'slug' => Str::slug($item['title']),
                'desc' => $item['desc'] ?? null,
                'image' => $item['image'] ?? null,
                'moral' => $item['data']['moral'] ?? null,
                'ages' => $item['ages'] ?? [],
                'agama' => $item['agama'] ?? [],
                'skills' => $item['skills'] ?? [],
                'data' => $item['data'] ?? [],
                'sort_order' => $maxOrder + $i + 1,
                'active' => true,
            ]);
        }
    }
}
