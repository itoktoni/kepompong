<?php
namespace Database\Seeders\Tables;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Command :
         * artisan seed:generate --mode=table --tables=activities
         *
         */

        $dataTables = [
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[1,2,3]',
                'created_by' => 1,
                'creator' => 'mimo',
                'data' => '{"tags":["buah","makanan","sehat"],"slides":[{"num":1,"nama":"Pisang","english":"Banana","image":"https:\\/\\/images.pexels.com\\/photos\\/2872767\\/pexels-photo-2872767.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung sebagai camilan sehat.","fungsi":"Memberi tenaga, rasanya manis dan lembut.","spesifikasi":"Bentuk panjang, kulit kuning, daging lunak.","fakta":"Mudah dikunyah dan disukai anak-anak."},{"num":2,"nama":"Apel","english":"Apple","image":"https:\\/\\/images.pexels.com\\/photos\\/102104\\/pexels-photo-102104.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat jus yang enak.","fungsi":"Baik untuk pencernaan dan daya tahan tubuh.","spesifikasi":"Bulat, kulit merah\\/hijau, daging renyah.","fakta":"Rasanya manis sedikit asam dan segar."},{"num":3,"nama":"Jeruk","english":"Orange","image":"https:\\/\\/images.pexels.com\\/photos\\/5987154\\/pexels-photo-5987154.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau diperas jadi jus.","fungsi":"Kaya vitamin C, menjaga tubuh tetap sehat.","spesifikasi":"Bulat, kulit oranye, daging berair.","fakta":"Rasanya asam manis dan sangat menyegarkan."},{"num":4,"nama":"Mangga","english":"Mango","image":"https:\\/\\/images.pexels.com\\/photos\\/14027299\\/pexels-photo-14027299.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat rujak.","fungsi":"Mengandung vitamin A baik untuk mata.","spesifikasi":"Bulat lonjong, kulit hijau\\/kuning, daging lembut.","fakta":"Semakin matang, rasanya semakin manis."},{"num":5,"nama":"Pepaya","english":"Papaya","image":"https:\\/\\/images.pexels.com\\/photos\\/32446430\\/pexels-photo-32446430.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau dicampur salad.","fungsi":"Membantu melancarkan pencernaan dengan baik.","spesifikasi":"Besar lonjong, kulit hijau\\/oranye, daging lembut.","fakta":"Dagingnya berwarna oranye dan rasanya manis."},{"num":6,"nama":"Alpukat","english":"Avocado","image":"https:\\/\\/images.pexels.com\\/photos\\/31833143\\/pexels-photo-31833143.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau dibuat jus.","fungsi":"Mengandung lemak baik untuk pertumbuhan.","spesifikasi":"Bulat, kulit hijau tua, daging lembut kental.","fakta":"Teksturnya halus dan rasanya gurih lembut."},{"num":7,"nama":"Semangka","english":"Watermelon","image":"https:\\/\\/images.pexels.com\\/photos\\/7284760\\/pexels-photo-7284760.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar saat cuaca panas.","fungsi":"Banyak airnya, menghilangkan rasa haus.","spesifikasi":"Besar bulat, kulit hijau bergaris, daging merah.","fakta":"Sebagian besar isinya adalah air yang segar."},{"num":8,"nama":"Nanas","english":"Pineapple","image":"https:\\/\\/images.pexels.com\\/photos\\/15554362\\/pexels-photo-15554362.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat rujak.","fungsi":"Membantu melancarkan pencernaan makanan.","spesifikasi":"Bulat lonjong, kulit kasar, daging kuning berair.","fakta":"Rasanya manis dan sedikit asam segar."},{"num":9,"nama":"Jambu Biji","english":"Guava","image":"https:\\/\\/images.pexels.com\\/photos\\/14027299\\/pexels-photo-14027299.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat jus.","fungsi":"Kaya vitamin C, menjaga daya tahan tubuh.","spesifikasi":"Bulat kecil, kulit hijau, daging putih\\/merah.","fakta":"Memiliki biji kecil dan rasanya manis segar."},{"num":10,"nama":"Stroberi","english":"Strawberry","image":"https:\\/\\/images.pexels.com\\/photos\\/33579054\\/pexels-photo-33579054.png?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau dicampur susu.","fungsi":"Kaya antioksidan baik untuk kesehatan.","spesifikasi":"Bentuk hati, kulit merah, ada biji di luar.","fakta":"Rasanya manis dan sedikit asam yang khas."},{"num":11,"nama":"Melon","english":"Melon","image":"https:\\/\\/images.pexels.com\\/photos\\/18281447\\/pexels-photo-18281447.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar sebagai pencuci mulut.","fungsi":"Banyak airnya, menyegarkan tenggorokan.","spesifikasi":"Bulat besar, kulit hijau, daging oranye.","fakta":"Rasanya manis lembut dan sangat berair."},{"num":12,"nama":"Kelapa","english":"Coconut","image":"https:\\/\\/images.pexels.com\\/photos\\/16077079\\/pexels-photo-16077079.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Airnya diminum, dagingnya dimakan.","fungsi":"Airnya mengganti cairan tubuh yang hilang.","spesifikasi":"Bulat keras, kulit cokelat, daging putih tebal.","fakta":"Air kelapa rasanya segar dan tidak asam."},{"num":13,"nama":"Belimbing","english":"Star Fruit","image":"https:\\/\\/images.pexels.com\\/photos\\/5987154\\/pexels-photo-5987154.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar atau dibuat rujak.","fungsi":"Membantu menjaga tekanan darah tetap stabil.","spesifikasi":"Bentuk memanjang, beruas, warna kuning\\/hijau.","fakta":"Jika dipotong melintang bentuknya seperti bintang."},{"num":14,"nama":"Sawo","english":"Sapodilla","image":"https:\\/\\/images.pexels.com\\/photos\\/32446430\\/pexels-photo-32446430.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung setelah matang sempurna.","fungsi":"Mengandung serat baik untuk pencernaan.","spesifikasi":"Bulat kecil, kulit cokelat kasar, daging cokelat.","fakta":"Rasanya sangat manis dan lembut di mulut."},{"num":15,"nama":"Durian","english":"Durian","image":"https:\\/\\/images.pexels.com\\/photos\\/5973583\\/pexels-photo-5973583.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan segar sebagai buah istimewa.","fungsi":"Memberi banyak energi dan rasa kenyang.","spesifikasi":"Besar, kulit berduri, daging kuning lembut.","fakta":"Baunya kuat tapi rasanya sangat manis lembut."},{"num":16,"nama":"Rambutan","english":"Rambutan","image":"https:\\/\\/images.pexels.com\\/photos\\/32446430\\/pexels-photo-32446430.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung setelah dikupas kulitnya.","fungsi":"Mengandung vitamin C dan zat gizi baik.","spesifikasi":"Bulat kecil, kulit berambut, daging putih bening.","fakta":"Rasanya manis segar dan sangat berair."},{"num":17,"nama":"Lengkeng","english":"Longan","image":"https:\\/\\/images.pexels.com\\/photos\\/16817763\\/pexels-photo-16817763.png?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung atau dikeringkan.","fungsi":"Membantu menambah tenaga dan kesegaran.","spesifikasi":"Bulat kecil, kulit cokelat halus, daging bening.","fakta":"Rasanya manis lembut dan tidak berair banyak."},{"num":18,"nama":"Manggis","english":"Mangosteen","image":"https:\\/\\/images.pexels.com\\/photos\\/17840025\\/pexels-photo-17840025.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung setelah dibuka kulitnya.","fungsi":"Kaya antioksidan menjaga tubuh tetap sehat.","spesifikasi":"Bulat, kulit ungu tebal, daging putih bersekat.","fakta":"Rasanya manis sedikit asam yang sangat segar."},{"num":19,"nama":"Kurma","english":"Date","image":"https:\\/\\/images.pexels.com\\/photos\\/14027299\\/pexels-photo-14027299.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan langsung sebagai camilan sehat.","fungsi":"Cepat memberi tenaga dan rasa kenyang.","spesifikasi":"Bentuk lonjong, kulit cokelat keriput, daging kenyal.","fakta":"Rasanya sangat manis dan cocok untuk anak."},{"num":20,"nama":"Delima","english":"Pomegranate","image":"https:\\/\\/images.pexels.com\\/photos\\/12027268\\/pexels-photo-12027268.jpeg?auto=compress&cs=tinysrgb&h=350","digunakan_untuk":"Dimakan bijinya atau dibuat jus.","fungsi":"Mengandung zat baik untuk jantung dan darah.","spesifikasi":"Bulat besar, kulit merah tebal, biji merah bening.","fakta":"Setiap bijinya berisi air yang rasanya manis segar."}]}',
                'desc' => 'Anak mengenal nama, ciri, dan rasa buah yang sehat.',
                'id' => 1023,
                'image' => 'https://images.pexels.com/photos/216565/pexels-photo-216565.jpeg?auto=compress&cs=tinysrgb&h=350',
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
                'views' => 2,
            ]
        ];

        DB::table("activities")->insert($dataTables);
    }
}