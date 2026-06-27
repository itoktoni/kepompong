<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermainanSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Ular Tangga Ajaib',
                'desc' => 'Permainan ular tangga sederhana untuk anak.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'sabar'],
                'data' => [
                    'players' => '2-4 anak',
                    'materials' => ['Papan ular tangga', 'Dadu', 'Pion warna-warni'],
                    'how' => 'Lempar dadu dan maju sesuai angka yang muncul. Jika mendarat di tangga, naik ke atas. Jika mendarat di kepala ular, turun ke bawah.',
                    'rules' => [
                        'Lempar dadu dan maju sesuai angka',
                        'Naik tangga jika mendarat di tangga',
                        'Turun ke bawah jika mendarat di kepala ular',
                        'Siapa duluan sampai finish, dia menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Lempar dadu dan maju sesuai angka'],
                        ['num' => 2, 'text' => 'Naik tangga jika mendarat di tangga'],
                        ['num' => 3, 'text' => 'Turun ke bawah jika mendarat di kepala ular'],
                        ['num' => 4, 'text' => 'Siapa duluan sampai finish, dia menang'],
                    ],
                    'moral' => 'Anak belajar sabar, sportif, dan berhitung.',
                ],
            ],
            [
                'title' => 'Petak Umpet',
                'desc' => 'Permainan tradisional petak umpet.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['berpikir_kreatif', 'kerjasama'],
                'data' => [
                    'players' => '3-8 anak',
                    'materials' => [],
                    'how' => 'Satu anak menjaga dan menghitung dengan mata tertutup. Anak lain bersembunyi. Penjaga mencari yang bersembunyi.',
                    'rules' => [
                        'Satu anak menjaga dan menghitung',
                        'Anak lain bersembunyi',
                        'Penjaga mencari yang bersembunyi',
                        'Yang ditemukan duluan jadi penjaga berikutnya',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Satu anak menjaga dan menghitung'],
                        ['num' => 2, 'text' => 'Anak lain bersembunyi'],
                        ['num' => 3, 'text' => 'Penjaga mencari yang bersembunyi'],
                        ['num' => 4, 'text' => 'Yang ditemukan duluan jadi penjaga berikutnya'],
                    ],
                    'moral' => 'Anak belajar strategi dan bersosialisasi.',
                ],
            ],
            [
                'title' => 'Congklak',
                'desc' => 'Permainan tradisional congklak Indonesia.',
                'ages' => [5, 6, 7, 8, 9],
                'skills' => ['berpikir_kreatif', 'strategi'],
                'data' => [
                    'players' => '2 anak',
                    'materials' => ['Papan congklak', 'Biji congklak'],
                    'how' => 'Ambil semua biji dari satu lubang, lalu bagikan satu per satu ke lubang berikutnya. Jika berhenti di lubang kosong, ambil biji lawan.',
                    'rules' => [
                        'Ambil semua biji dari satu lubang',
                        'Bagikan satu per satu ke lubang berikutnya',
                        'Jika berhenti di lubang kosong, ambil biji lawan',
                        'Paling banyak biji di rumah, dia menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Ambil semua biji dari satu lubang'],
                        ['num' => 2, 'text' => 'Bagikan satu per satu ke lubang berikutnya'],
                        ['num' => 3, 'text' => 'Jika berhenti di lubang kosong, ambil biji lawan'],
                        ['num' => 4, 'text' => 'Paling banyak biji di rumah, dia menang'],
                    ],
                    'moral' => 'Anak belajar berhitung dan berpikir strategis.',
                ],
            ],
            [
                'title' => 'Ular Naga',
                'desc' => 'Permainan tradisional ular naga yang seru dan menyenangkan.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['kerjasama', 'motorik_kasar'],
                'data' => [
                    'players' => '5-10 anak',
                    'materials' => [],
                    'how' => 'Anak-anak berbaris memegang bahu teman di depannya membentuk ular. Anak paling depan jadi kepala ular. Nyanyikan lagu Ular Naga sambil berjalan berkeliling.',
                    'rules' => [
                        'Semua anak berbaris memegang bahu teman',
                        'Anak paling depan jadi kepala ular',
                        'Bernyanyi lagu Ular Naga sambil jalan',
                        'Kepala ular berusaha menangkap ekor',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Berbaris dan pegang bahu teman'],
                        ['num' => 2, 'text' => 'Bernyanyi lagu Ular Naga'],
                        ['num' => 3, 'text' => 'Jalan berkeliling mengikuti irama'],
                        ['num' => 4, 'text' => 'Kepala ular berusaha menangkap ekor'],
                    ],
                    'moral' => 'Anak belajar bekerja sama dan mengikuti irama.',
                ],
            ],
            [
                'title' => 'Engklek',
                'desc' => 'Permainan tradisional engklek melompati kotak.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['motorik_kasar', 'berpikir_logis'],
                'data' => [
                    'players' => '2-4 anak',
                    'materials' => ['Kapur', 'Paving blok kecil'],
                    'how' => 'Gambar kotak-kotak di tanah. Lempar paving ke kotak, lalu lompati semua kotak tanpa menginjak garis. Ambil paving saat kembali.',
                    'rules' => [
                        'Lempar paving ke kotak yang dituju',
                        'Lompati semua kotak tanpa menginjak garis',
                        'Ambil paving saat kembali ke awal',
                        'Jika menginjak garis, giliran berikutnya',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Gambar kotak-kotak di tanah'],
                        ['num' => 2, 'text' => 'Lempar paving ke kotak yang dituju'],
                        ['num' => 3, 'text' => 'Lompati semua kotak tanpa menginjak garis'],
                        ['num' => 4, 'text' => 'Ambil paving saat kembali ke awal'],
                    ],
                    'moral' => 'Anak belajar keseimbangan dan ketepatan.',
                ],
            ],
            [
                'title' => 'Balap Karung',
                'desc' => 'Permainan balap karung yang seru dan lucu.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['motorik_kasar', 'sportif'],
                'data' => [
                    'players' => '4-8 anak',
                    'materials' => ['Karung goni'],
                    'how' => 'Setiap anak masuk ke dalam karung. Saat aba-aba, lompat menuju garis finish. Siapa duluan sampai, dia menang.',
                    'rules' => [
                        'Masuk ke dalam karung hingga pinggang',
                        'Lompat ke depan saat ada aba-aba',
                        'Tidak boleh keluar dari karung',
                        'Yang pertama sampai finish, dia menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Masuk ke dalam karung goni'],
                        ['num' => 2, 'text' => 'Dengarkan aba-aba mulai'],
                        ['num' => 3, 'text' => 'Lompat-lompat menuju garis finish'],
                        ['num' => 4, 'text' => 'Yang pertama sampai dia menang'],
                    ],
                    'moral' => 'Anak belajar sportif dan tidak mudah menyerah.',
                ],
            ],
            [
                'title' => 'Kelereng',
                'desc' => 'Permainan tradisional kelereng dengan jari.',
                'ages' => [5, 6, 7],
                'skills' => ['motorik_halus', 'konsentrasi'],
                'data' => [
                    'players' => '2-4 anak',
                    'materials' => ['Kelereng'],
                    'how' => 'Buat lingkaran di tanah. Setiap anak taruh kelereng di dalam lingkaran. Dorong kelereng pakai jari untuk menjatuhkan kelereng lawan.',
                    'rules' => [
                        'Buat lingkaran di tanah',
                        'Taruh kelereng di dalam lingkaran',
                        'Dorong kelereng untuk menjatuhkan kelereng lawan',
                        'Kelereng yang keluar lingkaran jadi milikmu',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Buat lingkaran di tanah'],
                        ['num' => 2, 'text' => 'Taruh kelereng masing-masing'],
                        ['num' => 3, 'text' => 'Dorong kelereng untuk menjatuhkan lawan'],
                        ['num' => 4, 'text' => 'Kelereng keluar jadi milikmu'],
                    ],
                    'moral' => 'Anak belajar fokus dan mengatur strategi.',
                ],
            ],
            [
                'title' => 'Gobak Sodor',
                'desc' => 'Permainan tradisional Gobak Sodor dari Indonesia.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['kerjasama', 'motorik_kasar'],
                'data' => [
                    'players' => '6-10 anak',
                    'materials' => ['Kapur untuk garis'],
                    'how' => 'Dua tim bermain. Satu tim jaga di garis, satu tim berusaha melewati semua garis dan kembali. Yang tersentuh jaga keluar.',
                    'rules' => [
                        'Dibagi menjadi dua tim',
                        'Satu tim jaga di setiap garis',
                        'Tim penyerang berusaha melewati semua garis',
                        'Yang tersentuh lawan keluar dari permainan',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Bagi menjadi dua tim'],
                        ['num' => 2, 'text' => 'Satu tim jaga di garis-garis'],
                        ['num' => 3, 'text' => 'Tim penyerang lari melewati garis'],
                        ['num' => 4, 'text' => 'Yang tersentuh keluar dari permainan'],
                    ],
                    'moral' => 'Anak belajar kerja sama tim dan strategi.',
                ],
            ],
            [
                'title' => 'Bola Bekel',
                'desc' => 'Permainan tradisional bola bekel yang menyenangkan.',
                'ages' => [5, 6, 7],
                'skills' => ['motorik_halus', 'konsentrasi'],
                'data' => [
                    'players' => '2-4 anak',
                    'materials' => ['Bola bekel', 'Biji bekel'],
                    'how' => 'Lempar bola ke atas, sebar biji bekel di lantai. Tangkap bola, lalu ambil biji satu per satu sambil menangkap bola lagi.',
                    'rules' => [
                        'Lempar bola ke atas',
                        'Sebar biji bekel di lantai',
                        'Ambil biji satu per satu sambil tangkap bola',
                        'Jika gagal tangkap bola, giliran berikutnya',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Lempar bola ke atas dan sebar biji'],
                        ['num' => 2, 'text' => 'Tangkap bola yang jatuh'],
                        ['num' => 3, 'text' => 'Ambil biji satu per satu'],
                        ['num' => 4, 'text' => 'Jika gagal, giliran berikutnya'],
                    ],
                    'moral' => 'Anak belajar koordinasi tangan dan mata.',
                ],
            ],
            [
                'title' => 'Cublak-Cublak Suweng',
                'desc' => 'Permainan tradisional cublak-cublak suweng dari Jawa.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['konsentrasi', 'kerjasama'],
                'data' => [
                    'players' => '4-6 anak',
                    'materials' => ['Kerikil kecil atau biji-bijian'],
                    'how' => 'Satu anak tiduran tengkurap. Anak lain duduk mengelilingi dan menyembunyikan kerikil. Anak yang tidur menebak siapa pegang kerikil.',
                    'rules' => [
                        'Satu anak tiduran tengkurap',
                        'Anak lain sembunyikan kerikil di tangan',
                        'Semua tangan ditaruh di punggung anak tidur',
                        'Tebak siapa yang pegang kerikil',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Satu anak tiduran tengkurap'],
                        ['num' => 2, 'text' => 'Anak lain sembunyikan kerikil'],
                        ['num' => 3, 'text' => 'Taruh tangan di punggung teman'],
                        ['num' => 4, 'text' => 'Tebak siapa yang pegang kerikil'],
                    ],
                    'moral' => 'Anak belajar mengamati dan menebak.',
                ],
            ],
            [
                'title' => 'Bentengan',
                'desc' => 'Permainan tradisional bentengan yang seru.',
                'ages' => [6, 7, 8],
                'skills' => ['kerjasama', 'motorik_kasar'],
                'data' => [
                    'players' => '6-12 anak',
                    'materials' => ['Tiang atau batu sebagai benteng'],
                    'how' => 'Dua tim masing-masing punya benteng. Berusaha menyentuh benteng lawan sambil menghindari tangkapan. Yang tersentuh jadi tahanan.',
                    'rules' => [
                        'Dibagi menjadi dua tim',
                        'Setiap tim punya benteng',
                        'Sentuh benteng lawan untuk menang',
                        'Yang tersentuh lawan jadi tahanan',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Bagi menjadi dua tim'],
                        ['num' => 2, 'text' => 'Setiap tim jaga bentengnya'],
                        ['num' => 3, 'text' => 'Berusaha sentuh benteng lawan'],
                        ['num' => 4, 'text' => 'Yang tersentuh jadi tahanan'],
                    ],
                    'moral' => 'Anak belajar strategi dan kerja sama tim.',
                ],
            ],
            [
                'title' => 'Lompat Tali',
                'desc' => 'Permainan tradisional lompat tali yang menyenangkan.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['motorik_kasar', 'konsentrasi'],
                'data' => [
                    'players' => '3-6 anak',
                    'materials' => ['Tali karet panjang'],
                    'how' => 'Dua anak memegang tali di kedua ujung. Anak lain melompati tali. Tali dinaikkan terus dari rendah ke tinggi.',
                    'rules' => [
                        'Dua anak memegang tali',
                        'Anak lain melompati tali',
                        'Tali dinaikkan setelah berhasil',
                        'Jika kena tali, giliran berikutnya',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Dua anak pegang tali di ujung'],
                        ['num' => 2, 'text' => 'Anak lain bersiap melompat'],
                        ['num' => 3, 'text' => 'Lompati tali tanpa kena'],
                        ['num' => 4, 'text' => 'Tali dinaikkan setelah berhasil'],
                    ],
                    'moral' => 'Anak belajar keseimbangan dan percaya diri.',
                ],
            ],
            [
                'title' => 'Main Kapal-Kapalan',
                'desc' => 'Permainan balapan perahu kertas di air.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'players' => '2-4 anak',
                    'materials' => ['Kertas lipat', 'Baskom air atau selokan kecil'],
                    'how' => 'Setiap anak membuat perahu dari kertas. Taruh perahu di air dan tiup perahu menuju garis finish.',
                    'rules' => [
                        'Buat perahu dari kertas',
                        'Taruh perahu di air',
                        'Tiup perahu menuju finish',
                        'Perahu pertama sampai finish menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Buat perahu dari kertas'],
                        ['num' => 2, 'text' => 'Taruh perahu di atas air'],
                        ['num' => 3, 'text' => 'Tiup perahu ke arah finish'],
                        ['num' => 4, 'text' => 'Perahu pertama sampai menang'],
                    ],
                    'moral' => 'Anak belajar kreatif dan sabar.',
                ],
            ],
            [
                'title' => 'Tebak Kata',
                'desc' => 'Permainan menebak kata dari petunjuk.',
                'ages' => [5, 6, 7],
                'skills' => ['berpikir_logis', 'kosakata'],
                'data' => [
                    'players' => '3-6 anak',
                    'materials' => [],
                    'how' => 'Satu anak memberikan petunjuk tentang suatu benda atau hewan. Anak lain berusaha menebak kata yang dimaksud.',
                    'rules' => [
                        'Satu anak memberi petunjuk',
                        'Anak lain berusaha menebak',
                        'Petunjuk tidak boleh langsung menyebut namanya',
                        'Yang berhasil menebak jadi pemberi petunjuk berikutnya',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Satu anak jadi pemberi petunjuk'],
                        ['num' => 2, 'text' => 'Beri petunjuk tentang sesuatu'],
                        ['num' => 3, 'text' => 'Teman-teman berusaha menebak'],
                        ['num' => 4, 'text' => 'Yang benar menebak jadi pemberi petunjuk'],
                    ],
                    'moral' => 'Anak belajar berpikir kreatif dan menambah kosakata.',
                ],
            ],
            [
                'title' => 'Simon Says',
                'desc' => 'Permainan mengikuti perintah Simon.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['konsentrasi', 'motorik_kasar'],
                'data' => [
                    'players' => '3-10 anak',
                    'materials' => [],
                    'how' => 'Satu anak jadi Simon. Simon memberi perintah yang diawali kata Simon Says. Jika tidak ada kata Simon Says, jangan ikuti.',
                    'rules' => [
                        'Satu anak jadi Simon',
                        'Ikuti perintah yang diawali Simon Says',
                        'Jangan ikuti perintah tanpa Simon Says',
                        'Yang salah keluar dari permainan',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Satu anak jadi Simon'],
                        ['num' => 2, 'text' => 'Simon bilang: Simon Says tepuk tangan'],
                        ['num' => 3, 'text' => 'Simon bilang tanpa Simon Says: lompat'],
                        ['num' => 4, 'text' => 'Yang salah ikut keluar dari permainan'],
                    ],
                    'moral' => 'Anak belajar mendengarkan dengan teliti.',
                ],
            ],
            [
                'title' => 'Estafet Kelereng',
                'desc' => 'Permainan estafet membawa kelereng dengan sendok.',
                'ages' => [5, 6, 7],
                'skills' => ['motorik_halus', 'kerjasama'],
                'data' => [
                    'players' => '6-10 anak',
                    'materials' => ['Kelereng', 'Sendok'],
                    'how' => 'Setiap anak membawa kelereng di sendok tanpa dipegang. Berjalan ke teman dan serahkan kelereng. Tim yang paling cepat menang.',
                    'rules' => [
                        'Pegang sendok dengan kelereng di atasnya',
                        'Jalan cepat ke teman berikutnya',
                        'Kelereng tidak boleh jatuh',
                        'Tim pertama yang selesai menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Taruh kelereng di sendok'],
                        ['num' => 2, 'text' => 'Jalan cepat ke teman berikutnya'],
                        ['num' => 3, 'text' => 'Serahkan kelereng ke teman'],
                        ['num' => 4, 'text' => 'Tim pertama selesai menang'],
                    ],
                    'moral' => 'Anak belajar konsentrasi dan kerja sama.',
                ],
            ],
            [
                'title' => 'Tarik Tambang',
                'desc' => 'Permainan tarik tambang yang seru dan kompak.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['kerjasama', 'motorik_kasar'],
                'data' => [
                    'players' => '6-12 anak',
                    'materials' => ['Tali tambang panjang'],
                    'how' => 'Dua tim berdiri berhadapan memegang tali. Saat aba-aba, tarik tali ke arah masing-masing. Tim yang menarik lawan melewati garis menang.',
                    'rules' => [
                        'Dua tim berdiri berhadapan',
                        'Pegang tali dengan erat',
                        'Tarik saat ada aba-aba',
                        'Tim yang menarik lawan melewati garis menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Dua tim berdiri berhadapan'],
                        ['num' => 2, 'text' => 'Pegang tali dengan erat'],
                        ['num' => 3, 'text' => 'Tarik tali saat aba-aba'],
                        ['num' => 4, 'text' => 'Tim yang menang menarik lawan'],
                    ],
                    'moral' => 'Anak belajar kekompakan dan kerja sama.',
                ],
            ],
            [
                'title' => 'Kucing dan Tikus',
                'desc' => 'Permainan tradisional kucing dan tikus dalam lingkaran.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['kerjasama', 'motorik_kasar'],
                'data' => [
                    'players' => '6-10 anak',
                    'materials' => [],
                    'how' => 'Anak-anak berdiri berpegangan tangan membentuk lingkaran. Satu anak jadi kucing, satu anak jadi tikus di dalam. Kucing berusaha menangkap tikus.',
                    'rules' => [
                        'Anak-anak buat lingkaran berpegangan tangan',
                        'Satu anak jadi kucing di luar',
                        'Satu anak jadi tikus di dalam lingkaran',
                        'Kucing berusaha masuk menangkap tikus',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Buat lingkaran berpegangan tangan'],
                        ['num' => 2, 'text' => 'Satu jadi kucing, satu jadi tikus'],
                        ['num' => 3, 'text' => 'Kucing berusaha masuk menangkap'],
                        ['num' => 4, 'text' => 'Tikus berlari menghindar'],
                    ],
                    'moral' => 'Anak belajar kerja sama dan refleks cepat.',
                ],
            ],
            [
                'title' => 'Congklak Tradisional',
                'desc' => 'Permainan congklak dengan papan dan biji.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'strategi'],
                'data' => [
                    'players' => '2 anak',
                    'materials' => ['Papan congklak', 'Biji congklak'],
                    'how' => 'Ambil semua biji dari satu lubang. Bagikan satu per satu ke lubang berikutnya. Jika berhenti di lubang kosong, giliran berakhir.',
                    'rules' => [
                        'Ambil semua biji dari satu lubang pilihan',
                        'Bagikan satu per satu ke lubang berikutnya',
                        'Jika berhenti di lubang kosong, giliran selesai',
                        'Kumpulkan biji sebanyak mungkin di rumah',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Pilih lubang dan ambil bijinya'],
                        ['num' => 2, 'text' => 'Bagikan satu per satu ke lubang lain'],
                        ['num' => 3, 'text' => 'Lanjutkan jika berhenti di lubang isi'],
                        ['num' => 4, 'text' => 'Kumpulkan biji di rumah sebanyaknya'],
                    ],
                    'moral' => 'Anak belajar berhitung dan berpikir strategis.',
                ],
            ],
            [
                'title' => 'Bakiak',
                'desc' => 'Permainan balapan bakiak kayu bersama-sama.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['kerjasama', 'keseimbangan'],
                'data' => [
                    'players' => '4-8 anak',
                    'materials' => ['Bakiak kayu besar'],
                    'how' => 'Dua atau tiga anak berdiri di atas bakiak kayu. Bersama-sama melangkah menuju garis finish. Tim yang paling cepat menang.',
                    'rules' => [
                        'Berdiri bersama di atas bakiak',
                        'Melangkah bersama-sama',
                        'Harus kompak langkahnya',
                        'Tim pertama sampai finish menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Berdiri bersama di atas bakiak'],
                        ['num' => 2, 'text' => 'Atur langkah bersama-sama'],
                        ['num' => 3, 'text' => 'Jalan kompak menuju finish'],
                        ['num' => 4, 'text' => 'Tim pertama sampai menang'],
                    ],
                    'moral' => 'Anak belajar kekompakan dan kerja sama.',
                ],
            ],
            [
                'title' => 'Dakon',
                'desc' => 'Permainan tradisional Dakon dari Jawa.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['berpikir_logis', 'strategi'],
                'data' => [
                    'players' => '2 anak',
                    'materials' => ['Papan dakon', 'Biji dakon'],
                    'how' => 'Mirip congklak. Ambil biji dari satu lubang, bagikan satu per satu. Kumpulkan biji sebanyak mungkin di lubang rumah.',
                    'rules' => [
                        'Ambil biji dari satu lubang',
                        'Bagikan satu per satu ke lubang berikutnya',
                        'Ambil biji lawan jika berhenti di lubang kosong',
                        'Biji terbanyak di rumah menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Pilih lubang dan ambil bijinya'],
                        ['num' => 2, 'text' => 'Bagikan satu per satu ke lubang lain'],
                        ['num' => 3, 'text' => 'Ambil biji lawan jika bisa'],
                        ['num' => 4, 'text' => 'Biji terbanyak di rumah menang'],
                    ],
                    'moral' => 'Anak belajar berhitung dan berpikir jauh ke depan.',
                ],
            ],
            [
                'title' => 'Egrang',
                'desc' => 'Permainan tradisional egrang atau jangkungan.',
                'ages' => [6, 7, 8],
                'skills' => ['keseimbangan', 'motorik_kasar'],
                'data' => [
                    'players' => '2-6 anak',
                    'materials' => ['Egrang bambu'],
                    'how' => 'Berdiri di atas egrang bambu. Pegang erat tali atau tiangnya. Jalan melangkah ke depan menuju garis finish.',
                    'rules' => [
                        'Berdiri di atas egrang dengan hati-hati',
                        'Pegang erat tali egrang',
                        'Melangkah ke depan satu per satu',
                        'Jika jatuh, bangun lagi dan lanjutkan',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Naik ke atas egrang bambu'],
                        ['num' => 2, 'text' => 'Pegang erat dan jaga keseimbangan'],
                        ['num' => 3, 'text' => 'Melangkah ke depan pelan-pelan'],
                        ['num' => 4, 'text' => 'Terus berjalan sampai finish'],
                    ],
                    'moral' => 'Anak belajar keseimbangan dan tidak mudah menyerah.',
                ],
            ],
            [
                'title' => 'Galah Asin',
                'desc' => 'Permainan tradisional Galah Asin yang seru.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['kerjasama', 'motorik_kasar'],
                'data' => [
                    'players' => '6-10 anak',
                    'materials' => ['Kapur untuk garis'],
                    'how' => 'Dua tim saling berhadapan di lapangan. Setiap tim berusaha melewati garis lawan dan kembali. Yang tersentuh lawan keluar.',
                    'rules' => [
                        'Dua tim saling berhadapan',
                        'Berlari melewati garis lawan',
                        'Sentuh lawan untuk mengeluarkannya',
                        'Tim yang berhasil melewati semua garis menang',
                    ],
                    'pages' => [
                        ['num' => 1, 'text' => 'Dua tim saling berhadapan'],
                        ['num' => 2, 'text' => 'Berlari melewati garis lawan'],
                        ['num' => 3, 'text' => 'Hati-hati jangan tersentuh'],
                        ['num' => 4, 'text' => 'Tim yang lolos semua menang'],
                    ],
                    'moral' => 'Anak belajar kerja sama, strategi, dan sportif.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'permainan',
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
