<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ColoringSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Mewarnai Kupu-Kupu',
                'desc' => 'Mewarnai gambar kupu-kupu dengan krayon.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Kupu-kupu',
                    'suggested_colors' => ['Jingga', 'Biru', 'Kuning', 'Hijau'],
                    'steps' => [
                        'Pilih krayon warna yang kamu suka',
                        'Warnai sayap kupu-kupu dengan warna cerah',
                        'Warnai badan kupu-kupu dengan warna gelap',
                        'Tambahkan pola titik-titik di sayap',
                        'Warnai latar belakang dengan warna langit atau rumput',
                    ],
                    'moral' => 'Anak belajar mengenal warna dan melatih kreativitas.',
                ],
            ],
            [
                'title' => 'Mewarnai Pelangi',
                'desc' => 'Mewarnai gambar pelangi dengan tujuh warna.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Pelangi',
                    'suggested_colors' => ['Merah', 'Jingga', 'Kuning', 'Hijau', 'Biru', 'Nila', 'Ungu'],
                    'steps' => [
                        'Warnai lengkungan terluar dengan warna merah',
                        'Warnai lengkungan kedua dengan warna jingga',
                        'Lanjutkan dengan kuning, hijau, biru, nila, ungu',
                        'Warnai awan dengan warna putih atau abu-abu muda',
                        'Warnai matahari di sisi pelangi dengan warna kuning',
                    ],
                    'moral' => 'Anak belajar mengenal urutan warna pelangi.',
                ],
            ],
            [
                'title' => 'Mewarnai Rumah',
                'desc' => 'Mewarnai gambar rumah sederhana.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Rumah',
                    'suggested_colors' => ['Merah', 'Cokelat', 'Kuning', 'Hijau'],
                    'steps' => [
                        'Warnai dinding rumah dengan warna merah atau biru',
                        'Warnai atap rumah dengan warna cokelat atau merah',
                        'Warnai pintu dan jendela dengan warna cokelat',
                        'Warnai halaman rumah dengan warna hijau',
                        'Tambahkan matahari kuning di sudut gambar',
                    ],
                    'moral' => 'Anak belajar mengenal bagian-bagian rumah.',
                ],
            ],
            [
                'title' => 'Mewarnai Pohon',
                'desc' => 'Mewarnai gambar pohon besar.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Pohon',
                    'suggested_colors' => ['Hijau', 'Cokelat', 'Kuning'],
                    'steps' => [
                        'Warnai batang pohon dengan warna cokelat',
                        'Warnai daun pohon dengan warna hijau',
                        'Tambahkan buah dengan warna merah atau kuning',
                        'Warnai rumput di bawah pohon dengan hijau muda',
                        'Warnai langit di belakang dengan biru muda',
                    ],
                    'moral' => 'Anak belajar mengenal bagian-bagian pohon.',
                ],
            ],
            [
                'title' => 'Mewarnai Bunga Matahari',
                'desc' => 'Mewarnai gambar bunga matahari.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Bunga Matahari',
                    'suggested_colors' => ['Kuning', 'Cokelat', 'Hijau'],
                    'steps' => [
                        'Warnai kelopak bunga dengan warna kuning cerah',
                        'Warnai tengah bunga dengan warna cokelat',
                        'Warnai batang dan daun dengan warna hijau',
                        'Warnai latar belakang dengan warna langit biru',
                        'Tambahkan rumput hijau di bagian bawah',
                    ],
                    'moral' => 'Anak belajar mengenal bunga matahari.',
                ],
            ],
            [
                'title' => 'Mewarnai Kucing',
                'desc' => 'Mewarnai gambar kucing lucu.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Kucing',
                    'suggested_colors' => ['Oranye', 'Putih', 'Hitam', 'Hijau'],
                    'steps' => [
                        'Warnai badan kucing dengan warna oranye atau abu-abu',
                        'Warnai perut kucing dengan warna putih',
                        'Warnai mata kucing dengan warna hijau',
                        'Warnai kumis kucing dengan warna hitam',
                        'Warnai latar belakang sesuai seleramu',
                    ],
                    'moral' => 'Anak belajar menyayangi hewan peliharaan.',
                ],
            ],
            [
                'title' => 'Mewarnai Anjing',
                'desc' => 'Mewarnai gambar anjing yang lucu.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Anjing',
                    'suggested_colors' => ['Cokelat', 'Putih', 'Hitam'],
                    'steps' => [
                        'Warnai badan anjing dengan warna cokelat',
                        'Warnai perut dan moncong dengan warna putih',
                        'Warnai mata dan hidung dengan warna hitam',
                        'Warnai tali kalung anjing dengan warna merah',
                        'Warnai lantai atau rumput di bawah anjing',
                    ],
                    'moral' => 'Anak belajar mengenal hewan peliharaan setia.',
                ],
            ],
            [
                'title' => 'Mewarnai Ikan',
                'desc' => 'Mewarnai gambar ikan di laut.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Ikan',
                    'suggested_colors' => ['Biru', 'Oranye', 'Kuning', 'Hijau'],
                    'steps' => [
                        'Warnai badan ikan dengan warna oranye atau biru',
                        'Warnai sirip ikan dengan warna kuning',
                        'Warnai mata ikan dengan warna hitam',
                        'Warnai air di sekitar ikan dengan biru muda',
                        'Tambahkan gelembung udara dengan warna putih',
                    ],
                    'moral' => 'Anak belajar mengenal ikan dan habitatnya.',
                ],
            ],
            [
                'title' => 'Mewarnai Ayam',
                'desc' => 'Mewarnai gambar ayam jago.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Ayam',
                    'suggested_colors' => ['Merah', 'Kuning', 'Cokelat', 'Hijau'],
                    'steps' => [
                        'Warnai badan ayam dengan warna cokelat atau putih',
                        'Warnai jengger di kepala dengan warna merah',
                        'Warnai paruh dan kaki dengan warna kuning',
                        'Warnai ekor ayam dengan warna cokelat gelap',
                        'Warnai rumput di bawah ayam dengan hijau',
                    ],
                    'moral' => 'Anak belajar mengenal ayam dan cirinya.',
                ],
            ],
            [
                'title' => 'Mewarnai Gajah',
                'desc' => 'Mewarnai gambar gajah besar.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Gajah',
                    'suggested_colors' => ['Abu-abu', 'Putih', 'Merah Muda'],
                    'steps' => [
                        'Warnai badan gajah dengan warna abu-abu',
                        'Warnai gading gajah dengan warna putih',
                        'Warnai telinga bagian dalam dengan warna merah muda',
                        'Warnai kuku gajah dengan warna abu-abu muda',
                        'Warnai latar belakang dengan warna langit biru',
                    ],
                    'moral' => 'Anak belajar mengenal gajah, hewan darat terbesar.',
                ],
            ],
            [
                'title' => 'Mewarnai Jerapah',
                'desc' => 'Mewarnai gambar jerapah tinggi.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Jerapah',
                    'suggested_colors' => ['Kuning', 'Cokelat', 'Hijau'],
                    'steps' => [
                        'Warnai badan jerapah dengan warna kuning',
                        'Warnai bintik-bintik di badan dengan warna cokelat',
                        'Warnai kaki jerapah dengan warna cokelat muda',
                        'Warnai pohon di sebelah jerapah dengan hijau',
                        'Warnai langit dengan warna biru cerah',
                    ],
                    'moral' => 'Anak belajar mengenal jerapah, hewan tertinggi.',
                ],
            ],
            [
                'title' => 'Mewarnai Bintang Laut',
                'desc' => 'Mewarnai gambar bintang laut di pantai.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Bintang Laut',
                    'suggested_colors' => ['Oranye', 'Kuning', 'Biru', 'Krem'],
                    'steps' => [
                        'Warnai bintang laut dengan warna oranye atau merah',
                        'Warnai pasir pantai dengan warna krem atau kuning',
                        'Warnai air laut dengan warna biru',
                        'Warnai langit dengan warna biru muda',
                        'Tambahkan matahari kuning di sudut gambar',
                    ],
                    'moral' => 'Anak belajar mengenal bintang laut dan pantai.',
                ],
            ],
            [
                'title' => 'Mewarnai Pesawat',
                'desc' => 'Mewarnai gambar pesawat terbang.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Pesawat',
                    'suggested_colors' => ['Putih', 'Biru', 'Merah', 'Abu-abu'],
                    'steps' => [
                        'Warnai badan pesawat dengan warna putih',
                        'Warnai sayap pesawat dengan warna abu-abu',
                        'Warnai ekor pesawat dengan warna merah atau biru',
                        'Warnai jendela pesawat dengan warna biru tua',
                        'Warnai langit dengan warna biru cerah dan awan putih',
                    ],
                    'moral' => 'Anak belajar mengenal alat transportasi udara.',
                ],
            ],
            [
                'title' => 'Mewarnai Kapal',
                'desc' => 'Mewarnai gambar kapal di laut.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Kapal',
                    'suggested_colors' => ['Merah', 'Putih', 'Biru', 'Kuning'],
                    'steps' => [
                        'Warnai badan kapal dengan warna merah dan putih',
                        'Warnai tiang kapal dengan warna cokelat',
                        'Warnai bendera di tiang dengan warna merah putih',
                        'Warnai air laut dengan warna biru',
                        'Warnai langit dengan biru muda dan awan putih',
                    ],
                    'moral' => 'Anak belajar mengenal alat transportasi laut.',
                ],
            ],
            [
                'title' => 'Mewarnai Mobil',
                'desc' => 'Mewarnai gambar mobil.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Mobil',
                    'suggested_colors' => ['Merah', 'Hitam', 'Putih', 'Kuning'],
                    'steps' => [
                        'Warnai badan mobil dengan warna merah atau biru',
                        'Warnai roda mobil dengan warna hitam',
                        'Warnai jendela mobil dengan warna biru muda',
                        'Warnai lampu mobil dengan warna kuning',
                        'Warnai jalan di bawah mobil dengan warna abu-abu',
                    ],
                    'moral' => 'Anak belajar mengenal kendaraan di jalan.',
                ],
            ],
            [
                'title' => 'Mewarnai Motor',
                'desc' => 'Mewarnai gambar sepeda motor.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Motor',
                    'suggested_colors' => ['Merah', 'Hitam', 'Putih', 'Kuning'],
                    'steps' => [
                        'Warnai badan motor dengan warna merah atau hitam',
                        'Warnai roda motor dengan warna hitam',
                        'Warnai lampu motor dengan warna kuning',
                        'Warnai jok motor dengan warna hitam atau cokelat',
                        'Warnai jalan di bawah motor dengan abu-abu',
                    ],
                    'moral' => 'Anak belajar mengenal sepeda motor.',
                ],
            ],
            [
                'title' => 'Mewarnai Sepeda',
                'desc' => 'Mewarnai gambar sepeda.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Sepeda',
                    'suggested_colors' => ['Biru', 'Merah', 'Kuning', 'Hitam'],
                    'steps' => [
                        'Warnai rangka sepeda dengan warna biru atau merah',
                        'Warnai roda sepeda dengan warna hitam',
                        'Warnai jok sepeda dengan warna cokelat',
                        'Warnai stang sepeda dengan warna hitam',
                        'Warnai bunga di keranjang sepeda dengan warna kuning',
                    ],
                    'moral' => 'Anak belajar mengenal sepeda dan manfaatnya.',
                ],
            ],
            [
                'title' => 'Mewarnai Buah Semangka',
                'desc' => 'Mewarnai gambar buah semangka.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Buah Semangka',
                    'suggested_colors' => ['Merah', 'Hijau', 'Hitam', 'Putih'],
                    'steps' => [
                        'Warnai kulit semangka dengan warna hijau',
                        'Warnai daging semangka dengan warna merah',
                        'Gambar dan warnai biji semangka dengan warna hitam',
                        'Warnai garis di kulit semangka dengan hijau tua',
                        'Warnai piring di bawah semangka dengan warna putih',
                    ],
                    'moral' => 'Anak belajar mengenal buah semangka.',
                ],
            ],
            [
                'title' => 'Mewarnai Sayur Wortel',
                'desc' => 'Mewarnai gambar sayur wortel.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Sayur Wortel',
                    'suggested_colors' => ['Oranye', 'Hijau'],
                    'steps' => [
                        'Warnai badan wortel dengan warna oranye',
                        'Warnai daun wortel di atas dengan warna hijau',
                        'Tambahkan garis-garis kecil di badan wortel',
                        'Warnai latar belakang dengan warna kuning muda',
                        'Gambar tanah di bawah wortel dengan warna cokelat',
                    ],
                    'moral' => 'Anak belajar mengenal sayur wortel yang sehat.',
                ],
            ],
            [
                'title' => 'Mewarnai Es Krim',
                'desc' => 'Mewarnai gambar es krim yang lezat.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Es Krim',
                    'suggested_colors' => ['Merah Muda', 'Cokelat', 'Kuning', 'Putih'],
                    'steps' => [
                        'Warnai scoop es krim atas dengan warna merah muda',
                        'Warnai scoop es krim tengah dengan warna cokelat',
                        'Warnai cone atau waffle di bawah dengan warna kuning',
                        'Tambahkan taburan warna-warni di atas es krim',
                        'Warnai latar belakang dengan warna favoritmu',
                    ],
                    'moral' => 'Anak belajar mengenal makanan penutup yang disukai.',
                ],
            ],
            [
                'title' => 'Mewarnai Pelangi',
                'desc' => 'Mewarnai gambar pelangi indah.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['berpikir_kreatif', 'motorik_halus'],
                'data' => [
                    'subject' => 'Pelangi',
                    'suggested_colors' => ['Merah', 'Jingga', 'Kuning', 'Hijau', 'Biru', 'Nila', 'Ungu'],
                    'steps' => [
                        'Warnai lengkungan terluar dengan warna merah',
                        'Warnai lengkungan kedua dengan warna jingga',
                        'Lanjutkan dengan kuning, hijau, biru, nila, ungu',
                        'Warnai awan di sisi pelangi dengan warna putih',
                        'Warnai langit dengan warna biru cerah',
                    ],
                    'moral' => 'Anak belajar mengenal tujuh warna pelangi.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'coloring',
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
