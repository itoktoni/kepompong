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
        $dataTables = [
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[1,2,3]',
                'created_by' => 1,
                'creator' => 'mimo',
                'data' => '{
  "tags": ["buah","makanan","sehat"],
  "slides": [
        {"num":1,"nama":"Pisang","english":"Banana","image":"https:https://images.pexels.com/photos/33203199/pexels-photo-33203199.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung sebagai camilan sehat.","fungsi":"Memberi tenaga, rasanya manis dan lembut.","spesifikasi":"Bentuk panjang, kulit kuning, daging lunak.","fakta":"Mudah dikunyah dan disukai anak-anak."},
        {"num":2,"nama":"Apel","english":"Apple","image":"https:https://images.pexels.com/photos/7333124/pexels-photo-7333124.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat jus yang enak.","fungsi":"Baik untuk pencernaan dan daya tahan tubuh.","spesifikasi":"Bulat, kulit merah/hijau, daging renyah.","fakta":"Rasanya manis sedikit asam dan segar."},
        {"num":3,"nama":"Jeruk","english":"Orange","image":"https:https://images.pexels.com/photos/30925639/pexels-photo-30925639.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau diperas jadi jus.","fungsi":"Kaya vitamin C, menjaga tubuh tetap sehat.","spesifikasi":"Bulat, kulit oranye, daging berair.","fakta":"Rasanya asam manis dan sangat menyegarkan."},
        {"num":4,"nama":"Mangga","english":"Mango","image":"https:https://images.pexels.com/photos/17672881/pexels-photo-17672881.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat rujak.","fungsi":"Mengandung vitamin A baik untuk mata.","spesifikasi":"Bulat lonjong, kulit hijau/kuning, daging lembut.","fakta":"Semakin matang, rasanya semakin manis."},
        {"num":5,"nama":"Pepaya","english":"Papaya","image":"https:https://images.pexels.com/photos/28613331/pexels-photo-28613331.jpeg?auto=compress&cs=tinysrgb&h=350-background-yqJ3GUrnq7s?w=400&h=300&fit=crop","digunakan_untuk":"Dimakan langsung atau dicampur salad.","fungsi":"Membantu melancarkan pencernaan dengan baik.","spesifikasi":"Besar lonjong, kulit hijau/oranye, daging lembut.","fakta":"Dagingnya berwarna oranye dan rasanya manis."},
        {"num":6,"nama":"Alpukat","english":"Avocado","image":"https:https://images.pexels.com/photos/19808829/pexels-photo-19808829.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau dibuat jus.","fungsi":"Mengandung lemak baik untuk pertumbuhan.","spesifikasi":"Bulat, kulit hijau tua, daging lembut kental.","fakta":"Teksturnya halus dan rasanya gurih lembut."},
        {"num":7,"nama":"Semangka","english":"Watermelon","image":"https:https://images.pexels.com/photos/26950757/pexels-photo-26950757.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar saat cuaca panas.","fungsi":"Banyak airnya, menghilangkan rasa haus.","spesifikasi":"Besar bulat, kulit hijau bergaris, daging merah.","fakta":"Sebagian besar isinya adalah air yang segar."},
        {"num":8,"nama":"Nanas","english":"Pineapple","image":"https:https://images.pexels.com/photos/37284807/pexels-photo-37284807.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat rujak.","fungsi":"Membantu melancarkan pencernaan makanan.","spesifikasi":"Bulat lonjong, kulit kasar, daging kuning berair.","fakta":"Rasanya manis dan sedikit asam segar."},
        {"num":9,"nama":"Jambu Biji","english":"Guava","image":"https:https://images.pexels.com/photos/8668726/pexels-photo-8668726.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat jus.","fungsi":"Kaya vitamin C, menjaga daya tahan tubuh.","spesifikasi":"Bulat kecil, kulit hijau, daging putih/merah.","fakta":"Memiliki biji kecil dan rasanya manis segar."},
        {"num":10,"nama":"Stroberi","english":"Strawberry","image":"https:https://images.pexels.com/photos/31020869/pexels-photo-31020869.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau dicampur susu.","fungsi":"Kaya antioksidan baik untuk kesehatan.","spesifikasi":"Bentuk hati, kulit merah, ada biji di luar.","fakta":"Rasanya manis dan sedikit asam yang khas."},
        {"num":11,"nama":"Melon","english":"Melon","image":"https:https://images.pexels.com/photos/17903817/pexels-photo-17903817.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar sebagai pencuci mulut.","fungsi":"Banyak airnya, menyegarkan tenggorokan.","spesifikasi":"Bulat besar, kulit hijau, daging oranye.","fakta":"Rasanya manis lembut dan sangat berair."},
        {"num":12,"nama":"Kelapa","english":"Coconut","image":"https:https://images.pexels.com/photos/36693033/pexels-photo-36693033.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Airnya diminum, dagingnya dimakan.","fungsi":"Airnya mengganti cairan tubuh yang hilang.","spesifikasi":"Bulat keras, kulit cokelat, daging putih tebal.","fakta":"Air kelapa rasanya segar dan tidak asam."},
        {"num":13,"nama":"Belimbing","english":"Star Fruit","image":"https:https://images.pexels.com/photos/30893286/pexels-photo-30893286.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat rujak.","fungsi":"Membantu menjaga tekanan darah tetap stabil.","spesifikasi":"Bentuk memanjang, beruas, warna kuning/hijau.","fakta":"Jika dipotong melintang bentuknya seperti bintang."},
        {"num":14,"nama":"Sawo","english":"Sapodilla","image":"https:https://images.pexels.com/photos/3942502/pexels-photo-3942502.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung setelah matang sempurna.","fungsi":"Mengandung serat baik untuk pencernaan.","spesifikasi":"Bulat kecil, kulit cokelat kasar, daging cokelat.","fakta":"Rasanya sangat manis dan lembut di mulut."},
        {"num":15,"nama":"Durian","english":"Durian","image":"https:https://images.pexels.com/photos/37385943/pexels-photo-37385943.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar sebagai buah istimewa.","fungsi":"Memberi banyak energi dan rasa kenyang.","spesifikasi":"Besar, kulit berduri, daging kuning lembut.","fakta":"Baunya kuat tapi rasanya sangat manis lembut."},
        {"num":16,"nama":"Rambutan","english":"Rambutan","image":"https:https://images.pexels.com/photos/4869080/pexels-photo-4869080.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung setelah dikupas kulitnya.","fungsi":"Mengandung vitamin C dan zat gizi baik.","spesifikasi":"Bulat kecil, kulit berambut, daging putih bening.","fakta":"Rasanya manis segar dan sangat berair."},
        {"num":17,"nama":"Lengkeng","english":"Longan","image":"https:https://images.pexels.com/photos/36766999/pexels-photo-36766999.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau dikeringkan.","fungsi":"Membantu menambah tenaga dan kesegaran.","spesifikasi":"Bulat kecil, kulit cokelat halus, daging bening.","fakta":"Rasanya manis lembut dan tidak berair banyak."},
        {"num":18,"nama":"Manggis","english":"Mangosteen","image":"https:https://images.pexels.com/photos/32842293/pexels-photo-32842293.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung setelah dibuka kulitnya.","fungsi":"Kaya antioksidan menjaga tubuh tetap sehat.","spesifikasi":"Bulat, kulit ungu tebal, daging putih bersekat.","fakta":"Rasanya manis sedikit asam yang sangat segar."},
        {"num":19,"nama":"Kurma","english":"Date","image":"https:https://images.pexels.com/photos/17878136/pexels-photo-17878136.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung sebagai camilan sehat.","fungsi":"Cepat memberi tenaga dan rasa kenyang.","spesifikasi":"Bentuk lonjong, kulit cokelat keriput, daging kenyal.","fakta":"Rasanya sangat manis dan cocok untuk anak."},
    ]
}',
                'desc' => 'Anak mengenal nama, ciri, dan rasa buah yang sehat.',
                'image' => 'https://images.pexels.com/photos/12955695/pexels-photo-12955695.jpeg?auto=compress&cs=tinysrgb&h=350',
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["mengenal_benda","berbicara_sederhana","mengenal_rasa"]',
                'slug' => 'mengenal-buah-buahan',
                'sort_order' => 1,
                'status' => 'approved',
                'title' => 'Mengenal Buah-buahan',
                'type' => 'mengenal_kata',
                'views' => 1,
            ],
        ];


        Activity::where('type', ActivityType::mengenal_kata->value)->delete();

        foreach ($dataTables as $row) {
            DB::table('activities')->updateOrInsert(
                ['slug' => $row['slug'], 'type' => 'mengenal_kata'],
                $row
            );
        }
    }
}
