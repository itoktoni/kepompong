<?php

namespace Database\Seeders\Activity;

use App\ActivityType;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MengenalKataSeeder extends Seeder
{
    public function run(): void
    {
        $allSlides = [
            ["num"=>1,"nama"=>"Pisang","english"=>"Banana","image"=>null,"digunakan_untuk"=>"Dimakan langsung sebagai camilan sehat.","fungsi"=>"Memberi tenaga, rasanya manis dan lembut.","spesifikasi"=>"Bentuk panjang, kulit kuning, daging lunak.","fakta"=>"Mudah dikunyah dan disukai anak-anak."],
            ["num"=>2,"nama"=>"Apel","english"=>"Apple","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat jus yang enak.","fungsi"=>"Baik untuk pencernaan dan daya tahan tubuh.","spesifikasi"=>"Bulat, kulit merah/hijau, daging renyah.","fakta"=>"Rasanya manis sedikit asam dan segar."],
            ["num"=>3,"nama"=>"Jeruk","english"=>"Orange","image"=>null,"digunakan_untuk"=>"Dimakan langsung atau diperas jadi jus.","fungsi"=>"Kaya vitamin C, menjaga tubuh tetap sehat.","spesifikasi"=>"Bulat, kulit oranye, daging berair.","fakta"=>"Rasanya asam manis dan sangat menyegarkan."],
            ["num"=>4,"nama"=>"Mangga","english"=>"Mango","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat rujak.","fungsi"=>"Mengandung vitamin A baik untuk mata.","spesifikasi"=>"Bulat lonjong, kulit hijau/kuning, daging lembut.","fakta"=>"Semakin matang, rasanya semakin manis."],
            ["num"=>5,"nama"=>"Pepaya","english"=>"Papaya","image"=>null,"digunakan_untuk"=>"Dimakan langsung atau dicampur salad.","fungsi"=>"Membantu melancarkan pencernaan dengan baik.","spesifikasi"=>"Besar lonjong, kulit hijau/oranye, daging lembut.","fakta"=>"Dagingnya berwarna oranye dan rasanya manis."],
            ["num"=>6,"nama"=>"Alpukat","english"=>"Avocado","image"=>null,"digunakan_untuk"=>"Dimakan langsung atau dibuat jus.","fungsi"=>"Mengandung lemak baik untuk pertumbuhan.","spesifikasi"=>"Bulat, kulit hijau tua, daging lembut kental.","fakta"=>"Teksturnya halus dan rasanya gurih lembut."],
            ["num"=>7,"nama"=>"Semangka","english"=>"Watermelon","image"=>null,"digunakan_untuk"=>"Dimakan segar saat cuaca panas.","fungsi"=>"Banyak airnya, menghilangkan rasa haus.","spesifikasi"=>"Besar bulat, kulit hijau bergaris, daging merah.","fakta"=>"Sebagian besar isinya adalah air yang segar."],
            ["num"=>8,"nama"=>"Nanas","english"=>"Pineapple","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat rujak.","fungsi"=>"Membantu melancarkan pencernaan makanan.","spesifikasi"=>"Bulat lonjong, kulit kasar, daging kuning berair.","fakta"=>"Rasanya manis dan sedikit asam segar."],
            ["num"=>9,"nama"=>"Jambu Biji","english"=>"Guava","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat jus.","fungsi"=>"Kaya vitamin C, menjaga daya tahan tubuh.","spesifikasi"=>"Bulat kecil, kulit hijau, daging putih/merah.","fakta"=>"Memiliki biji kecil dan rasanya manis segar."],
            ["num"=>10,"nama"=>"Stroberi","english"=>"Strawberry","image"=>null,"digunakan_untuk"=>"Dimakan langsung atau dicampur susu.","fungsi"=>"Kaya antioksidan baik untuk kesehatan.","spesifikasi"=>"Bentuk hati, kulit merah, ada biji di luar.","fakta"=>"Rasanya manis dan sedikit asam yang khas."],
            ["num"=>11,"nama"=>"Melon","english"=>"Melon","image"=>null,"digunakan_untuk"=>"Dimakan segar sebagai pencuci mulut.","fungsi"=>"Banyak airnya, menyegarkan tenggorokan.","spesifikasi"=>"Bulat besar, kulit hijau, daging oranye.","fakta"=>"Rasanya manis lembut dan sangat berair."],
            ["num"=>12,"nama"=>"Kelapa","english"=>"Coconut","image"=>null,"digunakan_untuk"=>"Airnya diminum, dagingnya dimakan.","fungsi"=>"Airnya mengganti cairan tubuh yang hilang.","spesifikasi"=>"Bulat keras, kulit cokelat, daging putih tebal.","fakta"=>"Air kelapa rasanya segar dan tidak asam."],
            ["num"=>13,"nama"=>"Belimbing","english"=>"Star Fruit","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat rujak.","fungsi"=>"Membantu menjaga tekanan darah tetap stabil.","spesifikasi"=>"Bentuk memanjang, beruas, warna kuning/hijau.","fakta"=>"Jika dipotong melintang bentuknya seperti bintang."],
            ["num"=>14,"nama"=>"Sawo","english"=>"Sapodilla","image"=>null,"digunakan_untuk"=>"Dimakan langsung setelah matang sempurna.","fungsi"=>"Mengandung serat baik untuk pencernaan.","spesifikasi"=>"Bulat kecil, kulit cokelat kasar, daging cokelat.","fakta"=>"Rasanya sangat manis dan lembut di mulut."],
            ["num"=>15,"nama"=>"Durian","english"=>"Durian","image"=>null,"digunakan_untuk"=>"Dimakan segar sebagai buah istimewa.","fungsi"=>"Memberi banyak energi dan rasa kenyang.","spesifikasi"=>"Besar, kulit berduri, daging kuning lembut.","fakta"=>"Baunya kuat tapi rasanya sangat manis lembut."],
            ["num"=>16,"nama"=>"Rambutan","english"=>"Rambutan","image"=>null,"digunakan_untuk"=>"Dimakan langsung setelah dikupas kulitnya.","fungsi"=>"Mengandung vitamin C dan zat gizi baik.","spesifikasi"=>"Bulat kecil, kulit berambut, daging putih bening.","fakta"=>"Rasanya manis segar dan sangat berair."],
            ["num"=>17,"nama"=>"Lengkeng","english"=>"Longan","image"=>null,"digunakan_untuk"=>"Dimakan langsung atau dikeringkan.","fungsi"=>"Membantu menambah tenaga dan kesegaran.","spesifikasi"=>"Bulat kecil, kulit cokelat halus, daging bening.","fakta"=>"Rasanya manis lembut dan tidak berair banyak."],
            ["num"=>18,"nama"=>"Manggis","english"=>"Mangosteen","image"=>null,"digunakan_untuk"=>"Dimakan langsung setelah dibuka kulitnya.","fungsi"=>"Kaya antioksidan menjaga tubuh tetap sehat.","spesifikasi"=>"Bulat, kulit ungu tebal, daging putih bersekat.","fakta"=>"Rasanya manis sedikit asam yang sangat segar."],
            ["num"=>19,"nama"=>"Kurma","english"=>"Date","image"=>null,"digunakan_untuk"=>"Dimakan langsung sebagai camilan sehat.","fungsi"=>"Cepat memberi tenaga dan rasa kenyang.","spesifikasi"=>"Bentuk lonjong, kulit cokelat keriput, daging kenyal.","fakta"=>"Rasanya sangat manis dan cocok untuk anak."],
            ["num"=>20,"nama"=>"Anggur","english"=>"Grape","image"=>null,"digunakan_untuk"=>"Dimakan langsung atau dibuat jus.","fungsi"=>"Kaya antioksidan menjaga tubuh sehat.","spesifikasi"=>"Bulat kecil, kulit ungu/hijau, daging berair.","fakta"=>"Rasanya manis segar dan ukurannya kecil."],
            ["num"=>21,"nama"=>"Buah Naga","english"=>"Dragon Fruit","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat jus.","fungsi"=>"Membantu pencernaan dan kaya vitamin.","spesifikasi"=>"Bulat, kulit merah/pink, daging putih/merah dengan biji hitam.","fakta"=>"Rasanya manis lembut dan bentuknya unik."],
            ["num"=>22,"nama"=>"Sirsak","english"=>"Soursop","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat jus.","fungsi"=>"Kaya vitamin C dan membantu daya tahan tubuh.","spesifikasi"=>"Bulat lonjong, kulit hijau berduri lunak, daging putih.","fakta"=>"Rasanya manis asam dan sangat menyegarkan."],
            ["num"=>23,"nama"=>"Salak","english"=>"Snake Fruit","image"=>null,"digunakan_untuk"=>"Dimakan langsung setelah dikupas.","fungsi"=>"Mengandung serat baik untuk pencernaan.","spesifikasi"=>"Bulat kecil, kulit bersisik cokelat, daging putih keras.","fakta"=>"Kulitnya mirip sisik ular dan rasanya manis."],
            ["num"=>24,"nama"=>"Kedondong","english"=>"Ambarella","image"=>null,"digunakan_untuk"=>"Dimakan segar atau dibuat rujak.","fungsi"=>"Kaya vitamin C dan menyegarkan tubuh.","spesifikasi"=>"Bulat lonjong, kulit hijau, daging renyah.","fakta"=>"Rasanya asam segar dan cocok untuk rujak."],
        ];

        $activities = [
            [
                'title' => 'Mengenal Buah-buahan Dasar',
                'desc' => 'Anak mengenal nama dan ciri buah yang sering ditemui sehari-hari.',
                'ages' => [1, 2, 3],
                'skills' => ['mengenal_benda', 'berbicara_sederhana'],
                'count' => 3,
                'status' => 'approved',
            ],
            [
                'title' => 'Mengenal Buah-buahan Populer',
                'desc' => 'Anak mengenal nama, ciri, dan rasa buah yang populer di Indonesia.',
                'ages' => [2, 3, 4, 5],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'mengenal_rasa'],
                'count' => 8,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Buah-buahan Lengkap',
                'desc' => 'Anak mengenal berbagai jenis buah beserta manfaat dan cirinya.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'mengenal_rasa', 'mengenal_fungsi'],
                'count' => 15,
                'status' => 'pending',
            ],
            [
                'title' => 'Ensiklopedia Buah-buahan',
                'desc' => 'Anak mengenal puluhan jenis buah lengkap dengan fakta menariknya.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'mengenal_rasa', 'mengenal_fungsi', 'pengetahuan_umum'],
                'count' => 24,
                'status' => 'pending',
            ],
        ];

        Activity::where('type', ActivityType::mengenal_kata->value)->delete();

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($activities as $i => $act) {
            $slides = array_slice($allSlides, 0, $act['count']);
            $slides = array_values(array_map(function ($s) {
                $s['image'] = null;
                return $s;
            }, $slides));

            $data = [
                'tags' => ['buah', 'makanan', 'sehat'],
                'slides' => $slides,
            ];

            Activity::updateOrInsert(
                ['slug' => \Illuminate\Support\Str::slug($act['title']), 'type' => 'mengenal_kata'],
                [
                    'active' => 1,
                    'addon_id' => null,
                    'agama' => '[]',
                    'ages' => json_encode($act['ages']),
                    'created_by' => 1,
                    'creator' => 'mimo',
                    'data' => json_encode($data),
                    'desc' => $act['desc'],
                    'image' => null,
                    'moral' => null,
                    'notes' => null,
                    'plans' => null,
                    'prompt' => null,
                    'skills' => json_encode($act['skills']),
                    'slug' => \Illuminate\Support\Str::slug($act['title']),
                    'sort_order' => $maxOrder + $i + 1,
                    'status' => $act['status'],
                    'title' => $act['title'],
                    'type' => 'mengenal_kata',
                    'views' => 0,
                ]
            );
        }
    }
}
