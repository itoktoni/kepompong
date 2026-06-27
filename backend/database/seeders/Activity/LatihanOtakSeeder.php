<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LatihanOtakSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Ingat dan Sebutkan',
                'desc' => 'Latihan memori dengan mengingat benda-benda.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'memori'],
                'data' => [
                    'type' => 'memori',
                    'exercises' => [
                        ['items' => ['apel', 'pisang', 'anggur'], 'instruction' => 'Ingat 3 buah ini, tutup mata, sebutkan kembali'],
                        ['items' => ['merah', 'biru', 'kuning', 'hijau'], 'instruction' => 'Ingat 4 warna ini, tutup mata, sebutkan kembali'],
                        ['items' => ['kucing', 'anjing', 'burung', 'ikan', 'kelinci'], 'instruction' => 'Ingat 5 hewan ini, tutup mata, sebutkan kembali'],
                    ],
                    'moral' => 'Anak melatih daya ingat dan konsentrasi.',
                ],
            ],
            [
                'title' => 'Mencari yang Berbeda',
                'desc' => 'Anak mencari benda yang berbeda dari kelompoknya.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'perbedaan',
                    'exercises' => [
                        ['group' => '🔴🔴🔴🔵🔴', 'answer' => 'Biru (posisi ke-4)', 'hint' => 'Lihat warna yang beda'],
                        ['group' => '🍎🍎🍐🍎', 'answer' => 'Pear (posisi ke-3)', 'hint' => 'Lihat buah yang beda'],
                        ['group' => '🐱🐱🐱🐶🐱', 'answer' => 'Anjing (posisi ke-4)', 'hint' => 'Lihat hewan yang beda'],
                    ],
                    'moral' => 'Anak melatih ketelitian dan daya observasi.',
                ],
            ],
            [
                'title' => 'Ingat Warna',
                'desc' => 'Latihan memori dengan mengingat warna-warna.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'memori'],
                'data' => [
                    'type' => 'memori',
                    'exercises' => [
                        ['items' => ['merah', 'kuning', 'biru'], 'instruction' => 'Ingat 3 warna ini, tutup mata, sebutkan kembali'],
                        ['items' => ['hijau', 'oranye', 'ungu', 'putih'], 'instruction' => 'Ingat 4 warna ini, tutup mata, sebutkan kembali'],
                        ['items' => ['merah muda', 'cokelat', 'hitam', 'abu-abu', 'krem'], 'instruction' => 'Ingat 5 warna ini, tutup mata, sebutkan kembali'],
                    ],
                    'moral' => 'Anak melatih daya ingat warna.',
                ],
            ],
            [
                'title' => 'Pola Berulang',
                'desc' => 'Latihan mengenali dan melanjutkan pola.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'pola',
                    'exercises' => [
                        ['items' => ['merah', 'biru', 'merah', 'biru', '...'], 'instruction' => 'Lihat polanya, warna apa selanjutnya?'],
                        ['items' => ['segitiga', 'lingkaran', 'segitiga', 'lingkaran', '...'], 'instruction' => 'Lihat polanya, bentuk apa selanjutnya?'],
                        ['items' => ['besar', 'kecil', 'besar', 'kecil', '...'], 'instruction' => 'Lihat polanya, ukuran apa selanjutnya?'],
                    ],
                    'moral' => 'Anak belajar mengenali pola dan melanjutkannya.',
                ],
            ],
            [
                'title' => 'Cocokkan Pasangan',
                'desc' => 'Latihan mencocokkan benda dengan pasangannya.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'pencocokan',
                    'exercises' => [
                        ['items' => ['sepatu', 'kaus kaki', 'sandal', 'sepatu', 'kaus kaki', 'sandal'], 'instruction' => 'Cocokkan benda yang sama berpasangan'],
                        ['items' => ['gelas', 'piring', 'sendok', 'gelas', 'piring', 'sendok'], 'instruction' => 'Cocokkan benda dapur yang sama'],
                        ['items' => ['segitiga', 'lingkaran', 'kotak', 'segitiga', 'lingkaran', 'kotak'], 'instruction' => 'Cocokkan bentuk yang sama'],
                    ],
                    'moral' => 'Anak belajar mengenali kesamaan dan mencocokkan.',
                ],
            ],
            [
                'title' => 'Hitung Mundur',
                'desc' => 'Latihan berhitung mundur dari angka tertentu.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'hitung',
                    'exercises' => [
                        ['items' => ['10', '9', '8', '7', '...'], 'instruction' => 'Hitung mundur dari 10, angka apa selanjutnya?'],
                        ['items' => ['5', '4', '3', '...'], 'instruction' => 'Hitung mundur dari 5, angka apa selanjutnya?'],
                        ['items' => ['20', '18', '16', '...'], 'instruction' => 'Hitung mundur dari 20 dengan lompat 2, angka apa selanjutnya?'],
                    ],
                    'moral' => 'Anak belajar berhitung mundur dengan benar.',
                ],
            ],
            [
                'title' => 'Cari yang Hilang',
                'desc' => 'Latihan mencari benda yang hilang dari urutan.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'memori'],
                'data' => [
                    'type' => 'mencari',
                    'exercises' => [
                        ['items' => ['apel', 'pisang', '...', 'anggur'], 'instruction' => 'Buah apa yang hilang dari urutan ini?'],
                        ['items' => ['merah', 'kuning', '...', 'hijau'], 'instruction' => 'Warna apa yang hilang dari urutan ini?'],
                        ['items' => ['kucing', '...', 'burung', 'ikan'], 'instruction' => 'Hewan apa yang hilang dari urutan ini?'],
                    ],
                    'moral' => 'Anak belajar mengenali yang hilang dari pola.',
                ],
            ],
            [
                'title' => 'Urutkan Besar ke Kecil',
                'desc' => 'Latihan mengurutkan benda dari besar ke kecil.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'pengurutan',
                    'exercises' => [
                        ['items' => ['semangka', 'apel', 'anggur'], 'instruction' => 'Urutkan buah dari yang paling besar ke kecil'],
                        ['items' => ['gajah', 'kucing', 'tikus'], 'instruction' => 'Urutkan hewan dari yang paling besar ke kecil'],
                        ['items' => ['gunung', 'rumah', 'kotak'], 'instruction' => 'Urutkan dari yang paling tinggi ke rendah'],
                    ],
                    'moral' => 'Anak belajar membandingkan ukuran.',
                ],
            ],
            [
                'title' => 'Tebak Pola',
                'desc' => 'Latihan menebak pola yang belum selesai.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'pola',
                    'exercises' => [
                        ['items' => ['1', '2', '3', '...'], 'instruction' => 'Angka apa selanjutnya?'],
                        ['items' => ['2', '4', '6', '...'], 'instruction' => 'Angka apa selanjutnya? Lompat 2'],
                        ['items' => ['A', 'B', 'C', '...'], 'instruction' => 'Huruf apa selanjutnya?'],
                    ],
                    'moral' => 'Anak belajar menebak pola angka dan huruf.',
                ],
            ],
            [
                'title' => 'Ingat Urutan',
                'desc' => 'Latihan mengingat urutan kejadian.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['memori', 'berpikir_logis'],
                'data' => [
                    'type' => 'memori',
                    'exercises' => [
                        ['items' => ['bangun tidur', 'sikat gigi', 'sarapan', 'berangkat sekolah'], 'instruction' => 'Ingat urutan kegiatan pagi hari'],
                        ['items' => ['cuci tangan', 'ambil piring', 'makan', 'cuci piring'], 'instruction' => 'Ingat urutan kegiatan makan'],
                        ['items' => ['buka pintu', 'masuk kamar', 'ganti baju', 'istirahat'], 'instruction' => 'Ingat urutan kegiatan pulang sekolah'],
                    ],
                    'moral' => 'Anak belajar mengingat urutan kejadian.',
                ],
            ],
            [
                'title' => 'Cari Perbedaan',
                'desc' => 'Latihan mencari perbedaan antara dua gambar.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'berpikir_logis'],
                'data' => [
                    'type' => 'perbedaan',
                    'exercises' => [
                        ['items' => ['🔴🔵🔴🔴', '🔴🔴🔴🔴'], 'instruction' => 'Temukan yang berbeda di baris pertama'],
                        ['items' => ['🍎🍎🍐🍎', '🍎🍎🍎🍎'], 'instruction' => 'Temukan buah yang berbeda'],
                        ['items' => ['🐱🐱🐱🐶', '🐱🐱🐱🐱'], 'instruction' => 'Temukan hewan yang berbeda'],
                    ],
                    'moral' => 'Anak belajar teliti dan mengamati perbedaan.',
                ],
            ],
            [
                'title' => 'Susun Kata',
                'desc' => 'Latihan menyusun huruf menjadi kata.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'kosakata'],
                'data' => [
                    'type' => 'susun_kata',
                    'exercises' => [
                        ['items' => ['U', 'K', 'A', 'N'], 'instruction' => 'Susun huruf ini menjadi nama hewan'],
                        ['items' => ['A', 'R', 'U', 'M', 'H'], 'instruction' => 'Susun huruf ini menjadi nama tempat tinggal'],
                        ['items' => ['B', 'U', 'A', 'H'], 'instruction' => 'Susun huruf ini menjadi nama makanan dari pohon'],
                    ],
                    'moral' => 'Anak belajar menyusun huruf dan menambah kosakata.',
                ],
            ],
            [
                'title' => 'Berhitung Cepat',
                'desc' => 'Latihan berhitung cepat sederhana.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'hitung',
                    'exercises' => [
                        ['items' => ['2', '+', '3', '=', '?'], 'instruction' => 'Berapa hasilnya?'],
                        ['items' => ['5', '-', '2', '=', '?'], 'instruction' => 'Berapa hasilnya?'],
                        ['items' => ['4', '+', '4', '=', '?'], 'instruction' => 'Berapa hasilnya?'],
                    ],
                    'moral' => 'Anak belajar berhitung dengan cepat dan tepat.',
                ],
            ],
            [
                'title' => 'Logika Sederhana',
                'desc' => 'Latihan berpikir logis dengan soal sederhana.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'logika',
                    'exercises' => [
                        ['items' => ['ayam', 'telur', '...'], 'instruction' => 'Apa yang menetas dari telur ayam?'],
                        ['items' => ['bibit', 'tanaman', '...'], 'instruction' => 'Apa yang tumbuh dari bibit?'],
                        ['items' => ['awan', 'hujan', '...'], 'instruction' => 'Apa yang muncul setelah hujan?'],
                    ],
                    'moral' => 'Anak belajar berpikir logis dan sebab-akibat.',
                ],
            ],
            [
                'title' => 'Memori Gambar',
                'desc' => 'Latihan memori dengan mengingat gambar.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['memori', 'konsentrasi'],
                'data' => [
                    'type' => 'memori',
                    'exercises' => [
                        ['items' => ['🏠', '🌳', '☀️'], 'instruction' => 'Ingat 3 gambar ini, tutup mata, sebutkan kembali'],
                        ['items' => ['🚗', '🚌', '🚲', '✈️'], 'instruction' => 'Ingat 4 gambar ini, tutup mata, sebutkan kembali'],
                        ['items' => ['🐶', '🐱', '🐰', '🐦', '🐟'], 'instruction' => 'Ingat 5 gambar ini, tutup mata, sebutkan kembali'],
                    ],
                    'moral' => 'Anak melatih daya ingat visual.',
                ],
            ],
            [
                'title' => 'Tebak Suara',
                'desc' => 'Latihan menebak suara dari benda atau hewan.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'memori'],
                'data' => [
                    'type' => 'suara',
                    'exercises' => [
                        ['items' => ['guk guk', 'meong', 'kukuruyuk'], 'instruction' => 'Tebak suara hewan apa saja ini'],
                        ['items' => ['kresek', 'duk duk', 'srrr'], 'instruction' => 'Tebak suara benda apa saja ini'],
                        ['items' => ['brum brum', 'toot toot', 'kling kling'], 'instruction' => 'Tebak suara kendaraan apa saja ini'],
                    ],
                    'moral' => 'Anak belajar mengenali dan membedakan suara.',
                ],
            ],
            [
                'title' => 'Urutkan Cerita',
                'desc' => 'Latihan mengurutkan gambar cerita dengan benar.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'memori'],
                'data' => [
                    'type' => 'pengurutan',
                    'exercises' => [
                        ['items' => ['benih', 'tunas', 'pohon besar'], 'instruction' => 'Urutkan gambar pertumbuhan tanaman'],
                        ['items' => ['telur', 'ayam kecil', 'ayam besar'], 'instruction' => 'Urutkan gambar pertumbuhan ayam'],
                        ['items' => ['bayi', 'anak kecil', 'orang dewasa'], 'instruction' => 'Urutkan gambar pertumbuhan manusia'],
                    ],
                    'moral' => 'Anak belajar mengurutkan cerita dengan logis.',
                ],
            ],
            [
                'title' => 'Hitung Langkah',
                'desc' => 'Latihan berhitung langkah dan jarak.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'hitung',
                    'exercises' => [
                        ['items' => ['10 langkah', '5 langkah', '...'], 'instruction' => 'Jika sudah 10 langkah dan mundur 5, sisa berapa?'],
                        ['items' => ['3 langkah', '+', '4 langkah', '='], 'instruction' => 'Berapa total langkah yang ditempuh?'],
                        ['items' => ['8 langkah', '-', '3 langkah', '='], 'instruction' => 'Berapa sisa langkah?'],
                    ],
                    'moral' => 'Anak belajar berhitung dalam kehidupan nyata.',
                ],
            ],
            [
                'title' => 'Cari Pasangan Warna',
                'desc' => 'Latihan mencocokkan warna yang sama.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'pencocokan',
                    'exercises' => [
                        ['items' => ['🔴', '🔵', '🔴', '🔵'], 'instruction' => 'Cocokkan warna yang sama berpasangan'],
                        ['items' => ['🟡', '🟢', '🟡', '🟢'], 'instruction' => 'Cocokkan warna yang sama berpasangan'],
                        ['items' => ['🟣', '🟠', '🟣', '🟠'], 'instruction' => 'Cocokkan warna yang sama berpasangan'],
                    ],
                    'moral' => 'Anak belajar mengenali dan mencocokkan warna.',
                ],
            ],
            [
                'title' => 'Pola Musik',
                'desc' => 'Latihan mengenali pola dalam irama musik.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['konsentrasi', 'berpikir_logis'],
                'data' => [
                    'type' => 'pola',
                    'exercises' => [
                        ['items' => ['tepuk', 'tepuk', 'jentik', 'tepuk', 'tepuk', '...'], 'instruction' => 'Irama apa selanjutnya?'],
                        ['items' => ['ketuk', 'diam', 'ketuk', 'diam', '...'], 'instruction' => 'Irama apa selanjutnya?'],
                        ['items' => ['tepuk', 'tepuk', 'tepuk', 'jentik', 'tepuk', 'tepuk', 'tepuk', '...'], 'instruction' => 'Irama apa selanjutnya?'],
                    ],
                    'moral' => 'Anak belajar mengenali pola irama.',
                ],
            ],
            [
                'title' => 'Ingat Bentuk',
                'desc' => 'Latihan memori dengan mengingat bentuk-bentuk.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['memori', 'konsentrasi'],
                'data' => [
                    'type' => 'memori',
                    'exercises' => [
                        ['items' => ['segitiga', 'lingkaran', 'kotak'], 'instruction' => 'Ingat 3 bentuk ini, tutup mata, sebutkan kembali'],
                        ['items' => ['bintang', 'hati', 'segiempat', 'oval'], 'instruction' => 'Ingat 4 bentuk ini, tutup mata, sebutkan kembali'],
                        ['items' => ['segitiga', 'lingkaran', 'kotak', 'bintang', 'hati'], 'instruction' => 'Ingat 5 bentuk ini, tutup mata, sebutkan kembali'],
                    ],
                    'moral' => 'Anak melatih daya ingat bentuk.',
                ],
            ],
            [
                'title' => 'Teka-Teki Logika',
                'desc' => 'Latihan berpikir logis dengan teka-teki sederhana.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'konsentrasi'],
                'data' => [
                    'type' => 'logika',
                    'exercises' => [
                        ['items' => ['Aku punya tangan tapi tidak bisa tepuk', 'Jam dinding'], 'instruction' => 'Tebak, apa aku ini?'],
                        ['items' => ['Aku punya mata tapi tidak bisa melihat', 'Jarum'], 'instruction' => 'Tebak, apa aku ini?'],
                        ['items' => ['Aku punya gigi tapi tidak bisa menggigit', 'Sisir'], 'instruction' => 'Tebak, apa aku ini?'],
                    ],
                    'moral' => 'Anak belajar berpikir kreatif dan logis.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'latihan_otak',
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
