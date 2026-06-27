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
            [
                'title' => 'Pernapasan Pelangi',
                'desc' => 'Latihan pernapasan sambil membayangkan warna pelangi.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk nyaman dan tutup mata',
                        'Tarik napas pelan sambil bayangkan warna merah',
                        'Hembuskan napas pelan sambil bayangkan warna jingga',
                        'Lanjutkan dengan kuning, hijau, biru, nila, ungu',
                        'Buka mata dan rasakan tubuh jadi tenang',
                    ],
                    'moral' => 'Anak belajar menenangkan diri dengan membayangkan warna.',
                ],
            ],
            [
                'title' => 'Mendengarkan Detak Jantung',
                'desc' => 'Latihan fokus mendengarkan detak jantung sendiri.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'mengenal_diri'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk atau berbaring dengan tenang',
                        'Letakkan tangan di dada kiri',
                        'Tutup mata dan dengarkan detak jantungmu',
                        'Hitung berapa kali jantung berdetak dalam satu menit',
                        'Rasakan betapa berharganya tubuhmu',
                    ],
                    'moral' => 'Anak belajar mengenal tubuhnya sendiri dan bersyukur.',
                ],
            ],
            [
                'title' => 'Pelukan Diri Sendiri',
                'desc' => 'Latihan memberikan kasih sayang kepada diri sendiri.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['mengenal_diri', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Berdiri atau duduk dengan nyaman',
                        'Buka kedua tangan lebar-lebar',
                        'Peluk dirimu sendiri dengan erat',
                        'Katakan dalam hati: aku sayang diriku',
                        'Ulangi pelukan sambil tersenyum',
                    ],
                    'moral' => 'Anak belajar mencintai dan menghargai diri sendiri.',
                ],
            ],
            [
                'title' => 'Jalan Perlahan',
                'desc' => 'Latihan berjalan pelan-pelan sambil merasakan setiap langkah.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'kesadaran_tubuh'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Lepas sepatu dan berdiri di lantai',
                        'Mulai berjalan sangat pelan',
                        'Rasakan kaki menyentuh lantai',
                        'Hitung setiap langkah sampai sepuluh',
                        'Berhenti dan rasakan tubuh jadi ringan',
                    ],
                    'moral' => 'Anak belajar fokus dan sadar akan gerakan tubuh.',
                ],
            ],
            [
                'title' => 'Mengamati Awan',
                'desc' => 'Latihan mengamati bentuk awan di langit.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'berpikir_kreatif'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Berbaring di rumput atau tikar di luar rumah',
                        'Lihat ke langit dan temukan awan',
                        'Amati bentuk awan, mirip apa?',
                        'Ceritakan apa yang kamu lihat',
                        'Tutup mata sebentar dan bayangkan awan itu terbang',
                    ],
                    'moral' => 'Anak belajar mengamati alam dan berimajinasi.',
                ],
            ],
            [
                'title' => 'Menulis Rasa Syukur',
                'desc' => 'Latihan menulis hal-hal yang disyukuri hari ini.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['bersyukur', 'literasi'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Ambil kertas dan pensil',
                        'Tulis tiga hal yang kamu syukuri hari ini',
                        'Baca tulisanmu dengan suara pelan',
                        'Tutup mata dan rasakan senangnya',
                        'Simpan kertas di tempat spesial',
                    ],
                    'moral' => 'Anak belajar bersyukur dengan menulis hal baik.',
                ],
            ],
            [
                'title' => 'Memeluk Boneka',
                'desc' => 'Latihan menenangkan diri dengan memeluk benda kesayangan.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['tenang', 'mengelola_emosi'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Ambil boneka atau bantal kesayanganmu',
                        'Duduk di tempat yang nyaman',
                        'Peluk boneka dengan erat',
                        'Tarik napas dalam dan hembuskan pelan',
                        'Katakan dalam hati: aku aman dan tenang',
                    ],
                    'moral' => 'Anak belajar menenangkan diri saat cemas atau sedih.',
                ],
            ],
            [
                'title' => 'Peregangan Pagi',
                'desc' => 'Latihan peregangan sederhana di pagi hari.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['kesadaran_tubuh', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Berdiri tegak dan tarik napas dalam',
                        'Angkat tangan ke atas sambil jinjit',
                        'Turunkan tangan dan buang napas pelan',
                        'Bungkuk ke depan dan sentuh ujung kaki',
                        'Kembali berdiri tegak dan tersenyum',
                    ],
                    'moral' => 'Anak belajar memulai hari dengan peregangan yang sehat.',
                ],
            ],
            [
                'title' => 'Meniup Gelembung Sabun',
                'desc' => 'Latihan pernapasan dengan meniup gelembung sabun.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['konsentrasi', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Siapkan air sabun dan alat tiup',
                        'Celupkan alat tiup ke air sabun',
                        'Tiup pelan-pelan sampai gelembung keluar',
                        'Amati gelembung terbang dan pecah',
                        'Ulangi sambil tarik napas dalam dan tiup pelan',
                    ],
                    'moral' => 'Anak belajar mengatur napas dengan cara yang menyenangkan.',
                ],
            ],
            [
                'title' => 'Mendengarkan Musik Tenang',
                'desc' => 'Latihan mendengarkan musik pelan dengan mata tertutup.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk di tempat yang nyaman',
                        'Putar musik yang pelan dan menenangkan',
                        'Tutup mata dan dengarkan alunan musik',
                        'Ikuti irama dengan tarik dan buang napas pelan',
                        'Buka mata perlahan saat musik selesai',
                    ],
                    'moral' => 'Anak belajar menenangkan diri melalui musik.',
                ],
            ],
            [
                'title' => 'Memijat Tangan Sendiri',
                'desc' => 'Latihan memijat jari dan tangan sendiri dengan lembut.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['kesadaran_tubuh', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk nyaman dan letakkan tangan di pangkuan',
                        'Pijat setiap jari satu per satu dengan lembut',
                        'Tekan telapak tangan dengan ibu jari',
                        'Putar pergelangan tangan pelan-pelan',
                        'Kepalkan tangan lalu buka, ulangi tiga kali',
                    ],
                    'moral' => 'Anak belajar rileks dan mengenal bagian tubuhnya.',
                ],
            ],
            [
                'title' => 'Mengamati Bunga',
                'desc' => 'Latihan mengamati bunga dengan penuh perhatian.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'mengenal_alam'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Ambil satu bunga dari taman',
                        'Amati warnanya dengan seksama',
                        'Cium baunya pelan-pelan',
                        'Sentuh kelopaknya dengan lembut',
                        'Katakan terima kasih pada alam',
                    ],
                    'moral' => 'Anak belajar menghargai keindahan alam.',
                ],
            ],
            [
                'title' => 'Bernapas Seperti Balon',
                'desc' => 'Latihan pernapasan membayangkan tubuh seperti balon.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['konsentrasi', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk nyaman dan tutup mata',
                        'Tarik napas lewat hidung pelan-pelan',
                        'Bayangkan badanmu mengembang seperti balon',
                        'Hembuskan napas lewat mulut pelan-pelan',
                        'Bayangkan balon mengempis dan tubuh jadi ringan',
                    ],
                    'moral' => 'Anak belajar mengatur napas untuk menenangkan diri.',
                ],
            ],
            [
                'title' => 'Menulis Surat untuk Diri Sendiri',
                'desc' => 'Latihan menulis pesan baik untuk diri sendiri.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['mengenal_diri', 'literasi'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Ambil kertas dan pensil warna',
                        'Tulis namamu di bagian atas',
                        'Tulis tiga hal baik tentang dirimu',
                        'Gambar senyum di bawah tulisan',
                        'Lipat dan simpan di bawah bantal',
                    ],
                    'moral' => 'Anak belajar menghargai kelebihan diri sendiri.',
                ],
            ],
            [
                'title' => 'Menatap Langit',
                'desc' => 'Latihan menatap langit sambil merasakan ketenangan.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'bersyukur'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Keluar rumah dan cari tempat nyaman',
                        'Duduk atau berbaring dan lihat ke langit',
                        'Amati warna langit dan bentuk awan',
                        'Rasakan angin di wajahmu',
                        'Katakan terima kasih untuk hari ini',
                    ],
                    'moral' => 'Anak belajar menghargai keindahan langit.',
                ],
            ],
            [
                'title' => 'Menyentuh Tanah',
                'desc' => 'Latihan grounding dengan menyentuh tanah langsung.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['kesadaran_tubuh', 'mengenal_alam'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Lepas sepatu dan kaus kaki',
                        'Berdiri di atas rumput atau tanah',
                        'Rasakan tanah di bawah kaki',
                        'Tekan kaki pelan-pelan ke tanah',
                        'Diam dan rasakan koneksi dengan bumi',
                    ],
                    'moral' => 'Anak belajar merasakan koneksi dengan alam.',
                ],
            ],
            [
                'title' => 'Mendengarkan Suara Air',
                'desc' => 'Latihan mendengarkan suara air yang mengalir.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk di dekat keran atau air mengalir',
                        'Tutup mata dan dengarkan suara air',
                        'Bayangkan air itu jernih dan bersih',
                        'Ikuti suara air dengan napas pelan',
                        'Buka mata dan rasakan ketenangan',
                    ],
                    'moral' => 'Anak belajar menenangkan diri dengan suara air.',
                ],
            ],
            [
                'title' => 'Menggambar Perasaan',
                'desc' => 'Latihan menggambar perasaan yang sedang dirasakan.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['mengenali_emosi', 'berpikir_kreatif'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Ambil kertas dan krayon',
                        'Tutup mata sebentar dan rasakan perasaanmu',
                        'Pilih warna yang cocok dengan perasaanmu',
                        'Gambar apa saja yang mewakili perasaanmu',
                        'Ceritakan gambarmu kepada Mama atau Papa',
                    ],
                    'moral' => 'Anak belajar mengungkapkan emosi melalui gambar.',
                ],
            ],
            [
                'title' => 'Meditasi Kupu-Kupu',
                'desc' => 'Latihan meditasi membayangkan menjadi kupu-kupu.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'berimajinasi'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Duduk nyaman dan tutup mata',
                        'Bayangkan kamu adalah kupu-kupu kecil',
                        'Terbang pelan di atas taman bunga',
                        'Hinggap di bunga dan hirup baunya',
                        'Terbang lagi dan buka mata perlahan',
                    ],
                    'moral' => 'Anak belajar rileks melalui imajinasi yang indah.',
                ],
            ],
            [
                'title' => 'Bersyukur Sebelum Tidur',
                'desc' => 'Latihan bersyukur sebelum tidur malam.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['bersyukur', 'tenang'],
                'data' => [
                    'duration' => '5 menit',
                    'steps' => [
                        'Berbaring di tempat tidur dan tutup mata',
                        'Ingat tiga hal baik yang terjadi hari ini',
                        'Katakan terima kasih dalam hati untuk setiap hal',
                        'Tarik napas dalam dan hembuskan pelan',
                        'Tersenyum dan tidur dengan tenang',
                    ],
                    'moral' => 'Anak belajar mengakhiri hari dengan rasa syukur.',
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
