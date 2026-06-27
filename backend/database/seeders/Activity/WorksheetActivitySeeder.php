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
            [
                'title' => 'Mengenal Huruf D-E-F',
                'desc' => 'Worksheet mengenal dan menulis huruf D, E, F.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'literasi'],
                'data' => [
                    'topic' => 'Huruf dan Abjad',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus huruf D'],
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus huruf E'],
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus huruf F'],
                        ['type' => 'match', 'content' => 'Cocokkan huruf besar dengan huruf kecil'],
                    ],
                    'moral' => 'Anak belajar mengenal dan menulis huruf D, E, F.',
                ],
            ],
            [
                'title' => 'Mengenal Angka 6-10',
                'desc' => 'Worksheet mengenal dan berhitung angka 6 sampai 10.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'matematika'],
                'data' => [
                    'topic' => 'Angka dan Berhitung',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus angka 6'],
                        ['type' => 'trace', 'content' => 'Ikuti garis putus-putus angka 7, 8, 9, 10'],
                        ['type' => 'count', 'content' => 'Hitung jumlah bintang dan tulis angkanya'],
                        ['type' => 'circle', 'content' => 'Lingkari angka yang lebih kecil'],
                    ],
                    'moral' => 'Anak belajar mengenal angka 6 sampai 10.',
                ],
            ],
            [
                'title' => 'Mengenal Bentuk Dasar',
                'desc' => 'Worksheet mengenal bentuk lingkaran, segitiga, dan persegi.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'mengenal_bentuk'],
                'data' => [
                    'topic' => 'Bentuk dan Geometri',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Ikuti garis bentuk lingkaran'],
                        ['type' => 'trace', 'content' => 'Ikuti garis bentuk segitiga'],
                        ['type' => 'match', 'content' => 'Cocokkan benda dengan bentuknya'],
                        ['type' => 'color', 'content' => 'Warnai semua lingkaran dengan warna biru'],
                    ],
                    'moral' => 'Anak belajar mengenal bentuk dasar geometri.',
                ],
            ],
            [
                'title' => 'Mengenal Warna Dasar',
                'desc' => 'Worksheet mengenal warna merah, biru, dan kuning.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_kreatif', 'mengenal_warna'],
                'data' => [
                    'topic' => 'Warna',
                    'exercises' => [
                        ['type' => 'color', 'content' => 'Warnai gambar apel dengan warna merah'],
                        ['type' => 'color', 'content' => 'Warnai gambar langit dengan warna biru'],
                        ['type' => 'match', 'content' => 'Cocokkan benda dengan warnanya'],
                        ['type' => 'draw', 'content' => 'Gambar bunga dan warnai dengan warna favoritmu'],
                    ],
                    'moral' => 'Anak belajar mengenal warna dasar.',
                ],
            ],
            [
                'title' => 'Menebalkan Huruf A-Z',
                'desc' => 'Worksheet menebalkan huruf A sampai Z.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'literasi'],
                'data' => [
                    'topic' => 'Huruf dan Abjad',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Tebalkan huruf A dengan pensil'],
                        ['type' => 'trace', 'content' => 'Tebalkan huruf B dan C dengan pensil'],
                        ['type' => 'trace', 'content' => 'Tebalkan huruf D, E, F dengan pensil'],
                        ['type' => 'trace', 'content' => 'Tebalkan huruf G, H, I dengan pensil'],
                    ],
                    'moral' => 'Anak belajar menulis huruf dengan menebalkan.',
                ],
            ],
            [
                'title' => 'Menggambar Garis Lurus',
                'desc' => 'Worksheet melatih menggambar garis lurus.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'koordinasi'],
                'data' => [
                    'topic' => 'Menggambar Dasar',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Ikuti garis lurus dari kiri ke kanan'],
                        ['type' => 'trace', 'content' => 'Ikuti garis lurus dari atas ke bawah'],
                        ['type' => 'draw', 'content' => 'Gambar garis lurus menghubungkan dua titik'],
                        ['type' => 'draw', 'content' => 'Gambar pagar dari garis-garis lurus'],
                    ],
                    'moral' => 'Anak belajar menggambar garis lurus dengan rapi.',
                ],
            ],
            [
                'title' => 'Menggambar Lingkaran',
                'desc' => 'Worksheet melatih menggambar lingkaran.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'koordinasi'],
                'data' => [
                    'topic' => 'Menggambar Dasar',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Ikuti garis lingkaran putus-putus'],
                        ['type' => 'draw', 'content' => 'Gambar lingkaran besar di kotak'],
                        ['type' => 'draw', 'content' => 'Gambar bola dengan lingkaran'],
                        ['type' => 'draw', 'content' => 'Gambar matahari dengan lingkaran dan garis'],
                    ],
                    'moral' => 'Anak belajar menggambar bentuk lingkaran.',
                ],
            ],
            [
                'title' => 'Mencocokkan Gambar',
                'desc' => 'Worksheet mencocokkan gambar yang sama.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'pengamatan'],
                'data' => [
                    'topic' => 'Mencocokkan',
                    'exercises' => [
                        ['type' => 'match', 'content' => 'Cocokkan gambar hewan yang sama'],
                        ['type' => 'match', 'content' => 'Cocokkan buah dengan pohonnya'],
                        ['type' => 'match', 'content' => 'Cocokkan bayangan dengan benda aslinya'],
                        ['type' => 'match', 'content' => 'Cocokkan bentuk yang sama'],
                    ],
                    'moral' => 'Anak belajar mengamati dan mencocokkan.',
                ],
            ],
            [
                'title' => 'Mengurutkan Angka',
                'desc' => 'Worksheet mengurutkan angka dari kecil ke besar.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'matematika'],
                'data' => [
                    'topic' => 'Angka dan Urutan',
                    'exercises' => [
                        ['type' => 'number_order', 'content' => 'Urutkan angka 1 sampai 5 dari kecil ke besar'],
                        ['type' => 'number_order', 'content' => 'Urutkan angka 6 sampai 10 dari kecil ke besar'],
                        ['type' => 'fill', 'content' => 'Isi angka yang hilang: 1, 2, ..., 5'],
                        ['type' => 'fill', 'content' => 'Isi angka yang hilang: 3, ..., 5, ..., 7'],
                    ],
                    'moral' => 'Anak belajar mengurutkan angka dengan benar.',
                ],
            ],
            [
                'title' => 'Menyambung Titik',
                'desc' => 'Worksheet menyambung titik-titik jadi gambar.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'berpikir_logis'],
                'data' => [
                    'topic' => 'Menyambung Titik',
                    'exercises' => [
                        ['type' => 'dot_to_dot', 'content' => 'Sambung titik 1 sampai 10 untuk membentuk bintang'],
                        ['type' => 'dot_to_dot', 'content' => 'Sambung titik 1 sampai 5 untuk membentuk rumah'],
                        ['type' => 'dot_to_dot', 'content' => 'Sambung titik huruf A'],
                        ['type' => 'dot_to_dot', 'content' => 'Sambung titik angka 1, 2, 3'],
                    ],
                    'moral' => 'Anak belajar menyambung titik dan mengenal angka.',
                ],
            ],
            [
                'title' => 'Mewarnai Pola',
                'desc' => 'Worksheet mewarnai pola berulang.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'berpikir_kreatif'],
                'data' => [
                    'topic' => 'Pola dan Urutan',
                    'exercises' => [
                        ['type' => 'pattern', 'content' => 'Warnai pola: merah, biru, merah, biru, ...'],
                        ['type' => 'pattern', 'content' => 'Warnai pola: kuning, kuning, hijau, kuning, kuning, hijau, ...'],
                        ['type' => 'pattern', 'content' => 'Lanjutkan pola: lingkaran, segitiga, lingkaran, segitiga, ...'],
                        ['type' => 'pattern', 'content' => 'Lanjutkan pola: besar, kecil, besar, kecil, ...'],
                    ],
                    'moral' => 'Anak belajar mengenal pola berulang.',
                ],
            ],
            [
                'title' => 'Melengkapi Gambar',
                'desc' => 'Worksheet melengkapi gambar yang setengah.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'berpikir_logis'],
                'data' => [
                    'topic' => 'Melengkapi Gambar',
                    'exercises' => [
                        ['type' => 'complete', 'content' => 'Gambar setengah bagian lain dari rumah'],
                        ['type' => 'complete', 'content' => 'Gambar setengah bagian lain dari pohon'],
                        ['type' => 'complete', 'content' => 'Gambar setengah bagian lain dari bunga'],
                        ['type' => 'complete', 'content' => 'Gambar setengah bagian lain dari matahari'],
                    ],
                    'moral' => 'Anak belajar melengkapi gambar dan simetri.',
                ],
            ],
            [
                'title' => 'Mengenal Huruf Kapital',
                'desc' => 'Worksheet mengenal huruf kapital A sampai Z.',
                'ages' => [3, 4, 5],
                'skills' => ['motorik_halus', 'literasi'],
                'data' => [
                    'topic' => 'Huruf Kapital',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Tebalkan huruf kapital A, B, C'],
                        ['type' => 'trace', 'content' => 'Tebalkan huruf kapital D, E, F'],
                        ['type' => 'match', 'content' => 'Cocokkan huruf kapital dengan huruf kecil'],
                        ['type' => 'circle', 'content' => 'Lingkari huruf kapital di antara huruf kecil'],
                    ],
                    'moral' => 'Anak belajar mengenal huruf kapital.',
                ],
            ],
            [
                'title' => 'Berhitung Benda',
                'desc' => 'Worksheet berhitung benda di sekitar.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'matematika'],
                'data' => [
                    'topic' => 'Berhitung Benda',
                    'exercises' => [
                        ['type' => 'count', 'content' => 'Hitung jumlah bola dan tulis angkanya'],
                        ['type' => 'count', 'content' => 'Hitung jumlah kucing dan tulis angkanya'],
                        ['type' => 'count', 'content' => 'Hitung jumlah bintang di gambar'],
                        ['type' => 'count', 'content' => 'Hitung jumlah apel di keranjang'],
                    ],
                    'moral' => 'Anak belajar berhitung benda di sekitar.',
                ],
            ],
            [
                'title' => 'Mencari Yang Berbeda',
                'desc' => 'Worksheet mencari gambar yang berbeda.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'pengamatan'],
                'data' => [
                    'topic' => 'Mencari Perbedaan',
                    'exercises' => [
                        ['type' => 'find_different', 'content' => 'Lingkari gambar yang berbeda dari yang lain'],
                        ['type' => 'find_different', 'content' => 'Lingkari warna yang berbeda'],
                        ['type' => 'find_different', 'content' => 'Lingkari ukuran yang berbeda'],
                        ['type' => 'find_different', 'content' => 'Lingkari bentuk yang berbeda'],
                    ],
                    'moral' => 'Anak belajar mengamati dan mencari perbedaan.',
                ],
            ],
            [
                'title' => 'Mengenal Simbol',
                'desc' => 'Worksheet mengenal simbol plus, minus, dan sama dengan.',
                'ages' => [4, 5],
                'skills' => ['berpikir_logis', 'matematika'],
                'data' => [
                    'topic' => 'Simbol Matematika',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Tebalkan simbol tambah (+)'],
                        ['type' => 'trace', 'content' => 'Tebalkan simbol kurang (-)'],
                        ['type' => 'trace', 'content' => 'Tebalkan simbol sama dengan (=)'],
                        ['type' => 'match', 'content' => 'Cocokkan simbol dengan namanya'],
                    ],
                    'moral' => 'Anak belajar mengenal simbol matematika dasar.',
                ],
            ],
            [
                'title' => 'Menghitung Jumlah',
                'desc' => 'Worksheet menghitung jumlah sederhana.',
                'ages' => [4, 5],
                'skills' => ['berpikir_logis', 'matematika'],
                'data' => [
                    'topic' => 'Penjumlahan Sederhana',
                    'exercises' => [
                        ['type' => 'count', 'content' => 'Hitung jumlah 2 apel + 1 apel = ... apel'],
                        ['type' => 'count', 'content' => 'Hitung jumlah 3 bintang + 2 bintang = ... bintang'],
                        ['type' => 'draw', 'content' => 'Gambar 4 bola + 1 bola = ... bola'],
                        ['type' => 'fill', 'content' => 'Isi hasil: 1 + 1 = ...'],
                    ],
                    'moral' => 'Anak belajar penjumlahan sederhana.',
                ],
            ],
            [
                'title' => 'Mengenal Arah',
                'desc' => 'Worksheet mengenal arah atas, bawah, kiri, kanan.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'koordinasi'],
                'data' => [
                    'topic' => 'Arah dan Posisi',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Gambar panah ke atas'],
                        ['type' => 'trace', 'content' => 'Gambar panah ke bawah, kiri, dan kanan'],
                        ['type' => 'follow', 'content' => 'Bola ada di atas kotak. Gambar di tempat yang benar'],
                        ['type' => 'follow', 'content' => 'Kucing ada di bawah meja. Gambar di tempat yang benar'],
                    ],
                    'moral' => 'Anak belajar mengenal arah dan posisi benda.',
                ],
            ],
            [
                'title' => 'Menulis Nama Sendiri',
                'desc' => 'Worksheet latihan menulis nama sendiri.',
                'ages' => [4, 5],
                'skills' => ['motorik_halus', 'literasi'],
                'data' => [
                    'topic' => 'Menulis Nama',
                    'exercises' => [
                        ['type' => 'trace', 'content' => 'Tebalkan huruf-huruf namamu'],
                        ['type' => 'write', 'content' => 'Tulis namamu di bawah huruf yang sudah ditebalkan'],
                        ['type' => 'draw', 'content' => 'Gambar wajahmu di sebelah namamu'],
                        ['type' => 'color', 'content' => 'Warnai bingkai di sekitar namamu'],
                    ],
                    'moral' => 'Anak belajar menulis nama sendiri.',
                ],
            ],
            [
                'title' => 'Mengenal Musim',
                'desc' => 'Worksheet mengenal musim hujan dan kemarau.',
                'ages' => [3, 4, 5],
                'skills' => ['berpikir_logis', 'mengenal_alam'],
                'data' => [
                    'topic' => 'Musim dan Cuaca',
                    'exercises' => [
                        ['type' => 'match', 'content' => 'Cocokkan gambar dengan musim hujan atau kemarau'],
                        ['type' => 'color', 'content' => 'Warnai gambar musim hujan dengan warna biru'],
                        ['type' => 'color', 'content' => 'Warnai gambar musim kemarau dengan warna kuning'],
                        ['type' => 'draw', 'content' => 'Gambar payung untuk musim hujan'],
                    ],
                    'moral' => 'Anak belajar mengenal musim di Indonesia.',
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
