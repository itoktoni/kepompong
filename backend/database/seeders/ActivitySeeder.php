<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            'storytelling' => [
                [
                    'title' => 'Anak Penolong',
                    'desc' => 'Riko menolong anak yang terjatuh di taman bermain.',
                    'moral' => 'Menolong orang lain tanpa pamrih adalah perbuatan terpuji.',
                    'image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=400&h=600&fit=crop',
                    'ages' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['peduli_sesama', 'empati'],
                    'data' => ['pages' => [
                        ['num' => 1, 'image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=400&h=600&fit=crop', 'text' => 'Riko sedang bermain di taman ketika mendengar suara tangisan.'],
                        ['num' => 2, 'image' => 'https://images.unsplash.com/photo-1518709766631-a6a7f45921c3?w=400&h=600&fit=crop', 'text' => 'Anak kecil bernama Tono terjatuh dari ayunan dan lututnya berdarah.'],
                        ['num' => 3, 'image' => 'https://images.unsplash.com/photo-1474511320723-9a56873571b7?w=400&h=600&fit=crop', 'text' => 'Riko segera berlari mendekati Tono. Kau baik-baik saja?'],
                        ['num' => 4, 'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=600&fit=crop', 'text' => 'Riko mengambil air dan membersihkan luka Tono dengan hati-hati.'],
                        ['num' => 5, 'image' => 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?w=400&h=600&fit=crop', 'text' => 'Riko mengantarkan Tono ke rumahnya dan memberitahu orang tuanya.'],
                        ['num' => 6, 'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=400&h=600&fit=crop', 'text' => 'Orang tua Tono berterima kasih. Riko pulang dengan hati senang.'],
                    ]],
                ],
                [
                    'title' => 'Ikan Nemo Yang Berani',
                    'desc' => 'Nemo kecil belajar berani menghadapi tantangan di laut.',
                    'moral' => 'Keberatan datang dari hati yang kuat.',
                    'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=400&h=600&fit=crop',
                    'ages' => [3, 4, 5, 6, 7, 8],
                    'skills' => ['berani_mencoba', 'kemandirian'],
                    'data' => ['pages' => [
                        ['num' => 1, 'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=400&h=600&fit=crop', 'text' => 'Nemo adalah ikan kecil yang tinggal di terumbu karang.'],
                        ['num' => 2, 'image' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=600&fit=crop', 'text' => 'Suatu hari, Nemo ingin berenang ke laut lepas sendirian.'],
                        ['num' => 3, 'image' => 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=600&fit=crop', 'text' => 'Papa Marlin melarangnya. Terlalu berbahaya! kata Papa.'],
                        ['num' => 4, 'image' => 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?w=400&h=600&fit=crop', 'text' => 'Nemo tetap pergi dan bertemu hiu yang menakutkan.'],
                        ['num' => 5, 'image' => 'https://images.unsplash.com/photo-1534766555764-ce878a5e3a2b?w=400&h=600&fit=crop', 'text' => 'Dengan keberanian, Nemo berhasil lolos dan kembali ke Papa.'],
                    ]],
                ],
            ],
            'bermain_peran' => [
                [
                    'title' => 'Dokter & Pasien Kecil',
                    'desc' => 'Anak kecil belajar tidak takut ke dokter.',
                    'moral' => 'Jangan takut ke dokter. Komunikasi yang baik membantu proses penyembuhan.',
                    'image' => 'https://images.unsplash.com/photo-1581594693702-fbdc51b2763b?w=400&h=600&fit=crop',
                    'ages' => [3, 4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['berani_bicara', 'mengelola_marah'],
                    'data' => [
                        'roles' => [
                            ['name' => 'Dokter', 'emoji' => '👨‍⚕️', 'desc' => 'Memeriksa pasien dengan sabar dan menjelaskan dengan baik.'],
                            ['name' => 'Pasien', 'emoji' => '🧒', 'desc' => 'Menceritakan keluhan dengan jujur dan tidak takut.'],
                        ],
                        'pages' => [
                            ['num' => 1, 'narrator' => 'Raka sakit perut. Ibu membawanya ke dokter.', 'dialog' => [['role' => 'Pasien', 'text' => 'Bu, aku takut ke dokter...'], ['role' => 'Ibu', 'text' => 'Tidak apa, Nak. Dokternya baik.']]],
                            ['num' => 2, 'narrator' => 'Dokter menyapa Raka dengan ramah.', 'dialog' => [['role' => 'Dokter', 'text' => 'Halo, Raka! Cerita dong, sakitnya di mana?'], ['role' => 'Pasien', 'text' => 'Perut saya sakit, Dok.']]],
                            ['num' => 3, 'narrator' => 'Dokter memeriksa perut Raka.', 'dialog' => [['role' => 'Dokter', 'text' => 'Ini cuma masuk angin. Nanti minum obat ya.'], ['role' => 'Pasien', 'text' => 'Baik, Dok. Terima kasih!']]],
                        ],
                    ],
                ],
            ],
            'permainan' => [
                [
                    'title' => 'Estafet Kata Baik',
                    'desc' => 'Permainan kata untuk melatih kosakata positif.',
                    'moral' => 'Banyak kata baik yang bisa kita gunakan setiap hari.',
                    'ages' => [3, 4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['mendengarkan', 'berpikir_kreatif'],
                    'data' => [
                        'how' => 'Duduk berkelompok. Orang pertama menyebut kata baik. Orang berikutnya menyebut kata yang diawali huruf terakhir kata sebelumnya.',
                        'rules' => ['Duduk berkelompok 3-5 orang', 'Sebut kata baik bergiliran', 'Kata baru harus diawali huruf terakhir kata sebelumnya', 'Tidak boleh mengulang kata'],
                    ],
                ],
            ],
            'monolog' => [
                [
                    'title' => 'Aku Bisa!',
                    'desc' => 'Monolog tentang keberanian di hari pertama sekolah.',
                    'ages' => [5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['berani_bicara', 'berani_mencoba'],
                    'data' => [
                        'script' => 'Halo semuanya! Namaku Adit. Hari ini aku mau cerita tentang hari pertamaku di sekolah baru. Aku sangat gugup. Tapi aku ingat pesan ibu, Jangan takut, kamu pasti bisa! Aku pun masuk ke kelas dengan senyuman. Ternyata, teman-teman sangat ramah. Sekarang aku punya banyak sahabat!',
                        'tips' => ['Berdiri tegak dan percaya diri', 'Bicara dengan jelas dan pelan', 'Gunakan ekspresi wajah', 'Tatap penonton'],
                    ],
                ],
            ],
            'proyek_kreatif' => [
                [
                    'title' => 'Lukisan Keluarga',
                    'desc' => 'Buat lukisan keluarga menggunakan cat air atau crayon.',
                    'image' => 'https://images.unsplash.com/photo-1513364776144-60967b0f8000?w=400&h=600&fit=crop',
                    'ages' => [3, 4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['berpikir_kreatif', 'quality_time'],
                    'data' => [
                        'duration' => '45 menit',
                        'difficulty' => 'Mudah',
                        'materials' => ['Kertas gambar besar', 'Crayon atau cat air', 'Pensil', 'Penghapus'],
                        'steps' => [
                            ['title' => 'Siapkan Bahan', 'desc' => 'Ambil kertas gambar besar. Siapkan crayon atau cat air.'],
                            ['title' => 'Gambar Keluarga', 'desc' => 'Mulai gambar anggota keluarga satu per satu.'],
                            ['title' => 'Warnai', 'desc' => 'Warnai setiap anggota keluarga dengan warna favorit.'],
                            ['title' => 'Tambah Latar', 'desc' => 'Gambar latar belakang: rumah, taman, atau tempat favorit keluarga.'],
                        ],
                    ],
                ],
            ],
            'musik_gerak' => [
                [
                    'title' => 'Lagu Berhitung',
                    'desc' => 'Lagu sambil belajar berhitung 1-10.',
                    'ages' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['berpikir_kreatif', 'rutin_belajar'],
                    'data' => [
                        'lyrics' => '🎵 Satu dua tiga empat lima,\nEnam tujuh delapan sembilan,\nAyo kita berhitung bersama,\nBelajar itu menyenangkan! 🎵',
                        'moves' => ['Angkat jari satu per satu', 'Hitung dengan jari kedua tangan', 'Bertepuk saat menyebut angka', 'Melompat kecil saat refrain'],
                        'moral' => 'Belajar sambil bernyanyi membuat mudah diingat!',
                    ],
                ],
                [
                    'title' => 'Tenangkan Hati',
                    'desc' => 'Lagu menenangkan untuk menemani waktu istirahat si kecil.',
                    'ages' => [1, 2, 3, 4, 5],
                    'skills' => ['mengelola_emosi', 'rutin_belajar'],
                    'data' => [
                        'audio_url' => 'https://audio.com/embed/audio/1848595889841253?theme=image',
                        'lyrics' => '🌙 Tenangkan hati, tutup mata,\nDengarkan angin berbisik lembut,\nMimpi indah menanti di sana,\nTidur nyenyak, sayangku... 🌙',
                        'moves' => ['Duduk santai dan pejamkan mata', 'Bernapas dalam-daman perlahan', 'Ayunkan tubuh ke kiri dan kanan pelan'],
                        'moral' => 'Musik yang tenang membantu anak rileks dan tidur lebih nyenyak.',
                    ],
                ],
            ],
            'puzzle' => [
                [
                    'title' => 'Teka-Teki Hewan',
                    'desc' => 'Teka-teki tentang hewan untuk melatih berpikir kreatif.',
                    'ages' => [3, 4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['berpikir_kreatif', 'berani_mencoba'],
                    'data' => [
                        'questions' => [
                            ['q' => 'Aku belang hitam oranye, buas dan suka sendirian. Siapa aku?', 'a' => 'Harimau!', 'hint' => 'Raja hutan yang punya loreng', 'emoji' => '🐅'],
                            ['q' => 'Aku bertanduk, suka makan rumput, dan menghasilkan susu. Siapa aku?', 'a' => 'Sapi!', 'hint' => 'Hewan ternak petani', 'emoji' => '🐄'],
                            ['q' => 'Aku punya sayap dan bisa terbang tinggi. Siapa aku?', 'a' => 'Burung!', 'hint' => 'Hewan yang bersarang di pohon', 'emoji' => '🐦'],
                        ],
                    ],
                ],
            ],
            'mindfulness' => [
                [
                    'title' => 'Jurnal Perasaan',
                    'desc' => 'Menulis dan menggambar perasaan hari ini.',
                    'ages' => [4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['mengenali_emosi', 'bersyukur'],
                    'data' => [
                        'steps' => ['Ambil kertas dan pensil', 'Gambar wajahmu hari ini (senyum, sedih, marah, tenang)', 'Tulis 3 hal yang membuatmu senang', 'Tulis 1 hal yang membuatmu sedih', 'Peluk orang tuamu dan ceritakan'],
                        'benefit' => 'Mengenali dan mengekspresikan perasaan dengan sehat.',
                    ],
                ],
            ],
            'outdoor' => [
                [
                    'title' => 'Berburu Harta Karun Alam',
                    'desc' => 'Petualangan mencari benda-benda menarik di alam.',
                    'ages' => [3, 4, 5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['olahraga_teratur', 'berimajinasi'],
                    'data' => [
                        'steps' => ['Bawa kantong kecil', 'Cari 5 daun berbeda bentuk', 'Cari 3 batu berbeda ukuran', 'Cari 1 bunga atau ranting', 'Kembali dan ceritakan apa yang ditemukan'],
                        'observation' => 'Perhatikan warna, bentuk, dan tekstur setiap benda.',
                    ],
                ],
            ],
            'ilmu_pengetahuan' => [
                [
                    'title' => 'Eksperimen Gunung Berapi',
                    'desc' => 'Membuat gunung berapi mini dari baking soda.',
                    'ages' => [5, 6, 7, 8, 9, 10, 11],
                    'skills' => ['berpikir_kreatif', 'berani_mencoba'],
                    'data' => [
                        'materials' => ['Botol plastik kecil', 'Baking soda', 'Cuka', 'Pewarna makanan', 'Sabun cair'],
                        'steps' => ['Masukkan baking soda ke botol', 'Tambahkan pewarna dan sabun', 'Tuangkan cuka perlahan', 'Lihat reaksinya!'],
                        'explanation' => 'Reaksi kimia antara baking soda (basa) dan cuka (asam) menghasilkan gas CO2 yang membuat busa.',
                    ],
                ],
            ],
        ];

        $sortOrder = 0;
        foreach ($samples as $type => $items) {
            foreach ($items as $item) {
                Activity::create([
                    'type' => $type,
                    'title' => $item['title'],
                    'slug' => Str::slug($item['title']),
                    'desc' => $item['desc'] ?? null,
                    'image' => $item['image'] ?? null,
                    'moral' => $item['moral'] ?? null,
                    'ages' => $item['ages'] ?? [],
                    'skills' => $item['skills'] ?? [],
                    'data' => $item['data'] ?? [],
                    'sort_order' => $sortOrder++,
                    'active' => true,
                ]);
            }
        }
    }
}
