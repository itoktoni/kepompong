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
            [
                'title' => 'Tepuk Nyamuk',
                'desc' => 'Permainan tangan menepuk nyamuk dengan irama.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'konsentrasi'],
                'data' => [
                    'moves' => [
                        'Buka kedua telapak tangan',
                        'Tepuk tangan kanan ke tangan kiri',
                        'Tepuk tangan kiri ke tangan kanan',
                        'Tepuk cepat sambil bilang zzzz',
                        'Tangkap nyamuk dengan kedua tangan',
                    ],
                    'moral' => 'Anak belajar koordinasi tangan dan refleks.',
                ],
            ],
            [
                'title' => 'Jari-Jari Menari',
                'desc' => 'Permainan jari menari dengan gerakan lucu.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Angkat jari telunjuk dan goyangkan',
                        'Angkat jari tengah dan goyangkan',
                        'Angkat semua jari dan goyangkan bersama',
                        'Kepalkan tangan lalu buka cepat',
                        'Tari semua jari seperti menari balet',
                    ],
                    'moral' => 'Anak belajar menggerakkan jari dengan terkontrol.',
                ],
            ],
            [
                'title' => 'Palu dan Paku',
                'desc' => 'Permainan tangan meniru gerakan memalu paku.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'koordinasi'],
                'data' => [
                    'moves' => [
                        'Buat kepalan tangan sebagai palu',
                        'Satu tangan jadi paku, telunjuk lurus ke atas',
                        'Palu memukul paku pelan-pelan',
                        'Makin lama makin cepat',
                        'Paku masuk ke kayu, selesai!',
                    ],
                    'moral' => 'Anak belajar koordinasi dan ritme gerakan.',
                ],
            ],
            [
                'title' => 'Kupu-Kupu',
                'desc' => 'Permainan tangan membentuk kupu-kupu.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Kedua telapak tangan saling menempel',
                        'Angkat jari-jari seperti sayap kupu-kupu',
                        'Buka tutup jari seperti sayap mengepak',
                        'Terbangkan kupu-kupu ke kanan dan kiri',
                        'Kupu-kupu hinggap di bunga',
                    ],
                    'moral' => 'Anak belajar berimajinasi dan kreatif.',
                ],
            ],
            [
                'title' => 'Kelinci',
                'desc' => 'Permainan tangan membentuk telinga kelinci.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Buat kepalan tangan di atas kepala',
                        'Angkat dua jari telunjuk dan tengah',
                        'Goyangkan seperti telinga kelinci',
                        'Lompat-lompat kecil dengan jari',
                        'Kelinci makan wortel, buka tutup tangan',
                    ],
                    'moral' => 'Anak belajar bermain peran dengan tangan.',
                ],
            ],
            [
                'title' => 'Ular Tangga Tangan',
                'desc' => 'Permainan tangan membentuk ular dan tangga.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_logis'],
                'data' => [
                    'moves' => [
                        'Satu tangan lurus sebagai tangga',
                        'Tangan lain melengkung seperti ular',
                        'Jari naik naik di tangga selangkah demi selangkah',
                        'Ular datang, jari turun cepat',
                        'Jari selamat sampai di puncak',
                    ],
                    'moral' => 'Anak belajar bercerita dengan gerakan tangan.',
                ],
            ],
            [
                'title' => 'Tepuk Semut',
                'desc' => 'Permainan tepuk tangan dengan irama semut.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'konsentrasi'],
                'data' => [
                    'moves' => [
                        'Tepuk tangan pelan-pelan',
                        'Tepuk sedikit lebih cepat',
                        'Tepuk cepat seperti semut berlari',
                        'Tepuk sangat cepat sambil tertawa',
                        'Berhenti dan diam sejenak',
                    ],
                    'moral' => 'Anak belajar mengatur tempo dan irama.',
                ],
            ],
            [
                'title' => 'Awan dan Hujan',
                'desc' => 'Permainan tangan meniru gerakan awan dan hujan.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Angkat kedua tangan ke atas seperti awan',
                        'Goyangkan jari-jari turun seperti hujan',
                        'Tepuk paha untuk suara hujan makin deras',
                        'Kepalkan tangan, petir menyambar',
                        'Buka tangan pelan, hujan berhenti, matahari muncul',
                    ],
                    'moral' => 'Anak belajar meniru fenomena alam dengan tangan.',
                ],
            ],
            [
                'title' => 'Bintang Jatuh',
                'desc' => 'Permainan tangan meniru bintang jatuh.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Buka semua jari tangan kanan di atas',
                        'Goyangkan jari seperti bintang berkilau',
                        'Gerakkan tangan dari atas ke bawah cepat',
                        'Tangkap bintang dengan tangan kiri',
                        'Buka tangan kiri, bintang bersinar lagi',
                    ],
                    'moral' => 'Anak belajar berimajinasi tentang bintang.',
                ],
            ],
            [
                'title' => 'Matahari Terbit',
                'desc' => 'Permainan tangan meniru matahari terbit.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Kepalkan tangan di bawah seperti matahari belum terbit',
                        'Angkat tangan pelan-pelan ke atas',
                        'Buka jari-jari satu per satu',
                        'Kembangkan semua jari seperti sinar matahari',
                        'Goyangkan jari seperti sinar menyebar',
                    ],
                    'moral' => 'Anak belajar gerakan lambat dan terkontrol.',
                ],
            ],
            [
                'title' => 'Burung Terbang',
                'desc' => 'Permainan tangan meniru burung terbang.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Satu tangan lurus ke depan sebagai badan burung',
                        'Tangan lain direntangkan sebagai sayap',
                        'Kepakkan sayap naik turun',
                        'Terbangkan burung ke kanan dan kiri',
                        'Burung hinggap, tangan diam di pangkuan',
                    ],
                    'moral' => 'Anak belajar meniru gerakan hewan.',
                ],
            ],
            [
                'title' => 'Ikan Berenang',
                'desc' => 'Permainan tangan meniru ikan berenang.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Rapatkan kedua tangan seperti ikan',
                        'Gerakkan tangan ke kiri dan kanan',
                        'Jari-jari bergerak seperti ekor ikan',
                        'Renang cepat lalu pelan-pelan',
                        'Ikan melompat ke atas dari air',
                    ],
                    'moral' => 'Anak belajar meniru gerakan air dan ikan.',
                ],
            ],
            [
                'title' => 'Bunga Mekar',
                'desc' => 'Permainan tangan meniru bunga yang mekar.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Kepalkan kedua tangan seperti kuncup bunga',
                        'Buka jari satu per satu pelan-pelan',
                        'Kembangkan semua jari seperti kelopak bunga',
                        'Goyangkan jari seperti bunga tertiup angin',
                        'Tutup kembali jari seperti bunga menutup malam',
                    ],
                    'moral' => 'Anak belajar gerakan halus dan sabar.',
                ],
            ],
            [
                'title' => 'Pohon Bergoyang',
                'desc' => 'Permainan tangan meniru pohon bergoyang.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'keseimbangan'],
                'data' => [
                    'moves' => [
                        'Satu tangan lurus ke atas sebagai batang pohon',
                        'Tangan lain direntangkan sebagai dahan',
                        'Goyangkan dahan pelan seperti angin sepoi',
                        'Goyangkan lebih kencang seperti angin besar',
                        'Dahan diam kembali saat angin berhenti',
                    ],
                    'moral' => 'Anak belajar meniru gerakan alam.',
                ],
            ],
            [
                'title' => 'Kucing Menyisir',
                'desc' => 'Permainan tangan meniru kucing menjilat bulu.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Buat tangan seperti cakar kucing',
                        'Gerakkan tangan menjilati bulu di tangan lain',
                        'Sisir bulu dari atas ke bawah',
                        'Kucing duduk dan membersihkan diri',
                        'Kucing menggeliat dan tidur',
                    ],
                    'moral' => 'Anak belajar meniru kebiasaan hewan.',
                ],
            ],
            [
                'title' => 'Gajah Berjalan',
                'desc' => 'Permainan tangan meniru gajah berjalan.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Satu tangan ke bawah seperti belalai gajah',
                        'Tangan lain di belakang seperti ekor',
                        'Goyangkan belalai ke kiri dan kanan',
                        'Gajah berjalan lambat, berat dan besar',
                        'Gajah mandi, semprotkan air dari belalai',
                    ],
                    'moral' => 'Anak belajar meniru hewan besar dengan tangan.',
                ],
            ],
            [
                'title' => 'Kupu-Kupu Hinggap',
                'desc' => 'Permainan tangan meniru kupu-kupu hinggap di bunga.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Buka jari seperti sayap kupu-kupu',
                        'Terbangkan tangan ke kiri dan kanan',
                        'Kupu-kupu mendekati bunga',
                        'Hinggap pelan di atas bunga',
                        'Minum nektar lalu terbang lagi',
                    ],
                    'moral' => 'Anak belajar gerakan lembut dan terkontrol.',
                ],
            ],
            [
                'title' => 'Kodok Melompat',
                'desc' => 'Permainan tangan meniru kodok melompat.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'konsentrasi'],
                'data' => [
                    'moves' => [
                        'Kepalkan tangan seperti kodok diam',
                        'Buka tangan cepat seperti kodok melompat',
                        'Tepuk meja saat mendarat',
                        'Kodok diam sebentar, lalu lompat lagi',
                        'Kodok lompat ke kolam, plash!',
                    ],
                    'moral' => 'Anak belajar gerakan cepat dan refleks.',
                ],
            ],
            [
                'title' => 'Angin Bertiup',
                'desc' => 'Permainan tangan meniru angin bertiup.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Kedua tangan di depan mulut',
                        'Tiup pelan, tangan bergerak lembut',
                        'Tiup kencang, tangan bergerak cepat',
                        'Angin berhenti, tangan diam',
                        'Angin bertiup lagi dari arah lain',
                    ],
                    'moral' => 'Anak belajar mengatur kekuatan tiupan.',
                ],
            ],
            [
                'title' => 'Pelangi Tangan',
                'desc' => 'Permainan tangan meniru pelangi berwarna.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['motorik_halus', 'berpikir_kreatif'],
                'data' => [
                    'moves' => [
                        'Angkat tangan ke atas membentuk lengkungan',
                        'Goyangkan jari seperti warna merah',
                        'Goyangkan jari lebih cepat seperti warna kuning',
                        'Goyangkan jari pelan seperti warna biru',
                        'Pelangi menghilang, tangan turun pelan',
                    ],
                    'moral' => 'Anak belajar mengenal warna dan gerakan tangan.',
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
