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
            ["num"=>25,"nama"=>"Bayam","english"=>"Spinach","image"=>null,"digunakan_untuk"=>"Dimasak sayur bening atau ditumis.","fungsi"=>"Membuat tubuh kuat karena banyak zat besi.","spesifikasi"=>"Daun hijau, batangnya tipis, mudah dimasak.","fakta"=>"Bayam membuat tubuh kuat seperti Popeye!"],
            ["num"=>26,"nama"=>"Kubis","english"=>"Cabbage","image"=>null,"digunakan_untuk"=>"Dimasak sayur atau dibuat salad.","fungsi"=>"Kaya vitamin C yang baik untuk daya tahan tubuh.","spesifikasi"=>"Bulat besar, daunnya berlapis-lapis, warna hijau muda.","fakta"=>"Kubis bisa tahan lama kalau disimpan di kulkas."],
            ["num"=>27,"nama"=>"Bawang Merah","english"=>"Shallot","image"=>null,"digunakan_untuk"=>"Dipakai untuk bumbu masakan sehari-hari.","fungsi"=>"Membuat masakan jadi harum dan enak.","spesifikasi"=>"Bulat kecil, kulit merah cokelat, baunya kuat.","fakta"=>"Bawang merah bisa bikin mata menangis saat diiris!"],
            ["num"=>28,"nama"=>"Bawang Putih","english"=>"Garlic","image"=>null,"digunakan_untuk"=>"Dipakai untuk bumbu dasar masakan.","fungsi"=>"Membuat masakan gurih dan harum.","spesifikasi"=>"Bulat kecil, kulit putih, terdiri dari beberapa siung.","fakta"=>"Bawang putih sudah dipakai orang sejak ribuan tahun lalu!"],
            ["num"=>29,"nama"=>"Kentang","english"=>"Potato","image"=>null,"digunakan_untuk"=>"Digoreng, direbus, atau dibuat kentang tumbuk.","fungsi"=>"Mengandung karbohidrat yang memberi tenaga.","spesifikasi"=>"Bulat agak lonjong, kulit cokelat, daging kuning putih.","fakta"=>"Kentang adalah salah satu makanan pokok di banyak negara."],
            ["num"=>30,"nama"=>"Jagung","english"=>"Corn","image"=>null,"digunakan_untuk"=>"Direbus, dibakar, atau dibuat bubur jagung.","fungsi"=>"Kaya serat dan vitamin yang baik untuk pencernaan.","spesifikasi"=>"Bentuk memanjang, biji kuning berjajar rapi di tongkol.","fakta"=>"Jagung bisa dimakan langsung atau dijadikan popcorn!"],
            ["num"=>31,"nama"=>"Terong","english"=>"Eggplant","image"=>null,"digunakan_untuk"=>"Dimasak balado, ditumis, atau dibakar.","fungsi"=>"Mengandung serat baik untuk pencernaan.","spesifikasi"=>"Bentuk lonjong, kulit ungu mengkilap, daging lembut.","fakta"=>"Terong ada yang warnanya ungu, putih, atau hijau."],
            ["num"=>32,"nama"=>"Cabai","english"=>"Chili","image"=>null,"digunakan_untuk"=>"Dipakai untuk membuat masakan jadi pedas.","fungsi"=>"Mengandung vitamin C yang tinggi.","spesifikasi"=>"Bentuk kecil memanjang, warna merah atau hijau.","fakta"=>"Cabai bikin pedas karena ada zat bernama capsaicin!"],
            ["num"=>33,"nama"=>"Brokoli","english"=>"Broccoli","image"=>null,"digunakan_untuk"=>"Direbus, ditumis, atau dimakan mentah sebagai salad.","fungsi"=>"Kaya vitamin dan mineral yang membuat tubuh sehat.","spesifikasi"=>"Bentuknya seperti pohon kecil, warna hijau tua.","fakta"=>"Brokoli adalah salah satu sayuran paling sehat di dunia!"],
            ["num"=>34,"nama"=>"Jamur","english"=>"Mushroom","image"=>null,"digunakan_untuk"=>"Dimasak sup, ditumis, atau dibakar.","fungsi"=>"Mengandung protein nabati yang baik untuk tubuh.","spesifikasi"=>"Bentuknya seperti payung kecil, ada batang dan tudung.","fakta"=>"Jamur tumbuh di tempat yang lembap dan gelap."],
            ["num"=>35,"nama"=>"Kacang Panjang","english"=>"Long Beans","image"=>null,"digunakan_untuk"=>"Dimasak sayur, ditumis, atau dibuat lalapan.","fungsi"=>"Kaya serat dan vitamin yang baik untuk tubuh.","spesifikasi"=>"Bentuknya panjang seperti tali, warna hijau.","fakta"=>"Kacang panjang bisa tumbuh sangat panjang sampai setengah meter!"],
            ["num"=>36,"nama"=>"Lobak","english"=>"Radish","image"=>null,"digunakan_untuk"=>"Dimasak sup atau dibuat acar.","fungsi"=>"Membantu pencernaan dan menyegarkan tubuh.","spesifikasi"=>"Bentuknya lonjong, kulit putih, daging putih renyah.","fakta"=>"Lobak rasanya sedikit pedas dan segar saat dimakan mentah."],
            ["num"=>37,"nama"=>"Selada","english"=>"Lettuce","image"=>null,"digunakan_untuk"=>"Dimakan mentah sebagai salad atau lalapan.","fungsi"=>"Rendah kalori dan kaya air, baik untuk diet sehat.","spesifikasi"=>"Daunnya hijau muda, tipis, dan renyah.","fakta"=>"Selada sering dipakai sebagai pelengkap burger dan sandwich."],
            ["num"=>38,"nama"=>"Timun","english"=>"Cucumber","image"=>null,"digunakan_untuk"=>"Dimakan mentah, dibuat acar, atau jadi lalapan.","fungsi"=>"Banyak airnya, menyegarkan dan menghilangkan haus.","spesifikasi"=>"Bentuknya lonjong, kulit hijau, daging hijau muda berair.","fakta"=>"Timun bisa juga dipakai untuk masker wajah alami!"],
            ["num"=>39,"nama"=>"Tomat","english"=>"Tomato","image"=>null,"digunakan_untuk"=>"Dimakan mentah, dibuat jus, atau jadi bumbu masak.","fungsi"=>"Kaya vitamin C dan antioksidan untuk kulit sehat.","spesifikasi"=>"Bulat, kulit merah mengkilap, daging berair dan biji kecil.","fakta"=>"Tomat sebenarnya adalah buah, tapi sering dianggap sayur!"],
            ["num"=>40,"nama"=>"Wortel","english"=>"Carrot","image"=>null,"digunakan_untuk"=>"Dimakan mentah, dibuat jus, atau dimasak sup.","fungsi"=>"Kaya vitamin A yang sangat baik untuk kesehatan mata.","spesifikasi"=>"Bentuknya lonjong memanjang, warna oranye, ujungnya runcing.","fakta"=>"Wortel membuat mata tetap sehat dan bisa melihat jelas!"],
            ["num"=>41,"nama"=>"Labu","english"=>"Pumpkin","image"=>null,"digunakan_untuk"=>"Dimasak sup, dibuat kolak, atau dipanggang.","fungsi"=>"Kaya vitamin A dan serat yang baik untuk tubuh.","spesifikasi"=>"Bentuknya bulat besar, kulit oranye, daging kuning tebal.","fakta"=>"Labu bisa tumbuh sangat besar, bahkan sampai ratusan kilogram!"],
            ["num"=>42,"nama"=>"Daun Bawang","english"=>"Green Onion","image"=>null,"digunakan_untuk"=>"Dipakai sebagai taburan sup, mie, atau masakan.","fungsi"=>"Membuat masakan jadi lebih harum dan segar.","spesifikasi"=>"Bentuknya panjang seperti daun, warna hijau, ada bagian putih di bawah.","fakta"=>"Daun bawang adalah bawang yang masih muda dan hijau."],
            ["num"=>43,"nama"=>"Kol","english"=>"Cabbage","image"=>null,"digunakan_untuk"=>"Dimasak sayur, ditumis, atau dibuat acar.","fungsi"=>"Kaya vitamin C dan serat untuk daya tahan tubuh.","spesifikasi"=>"Bulat besar dan padat, daunnya hijau muda berlapis.","fakta"=>"Kol dan kubis sebenarnya adalah sayuran yang sama!"],
            ["num"=>44,"nama"=>"Paprika","english"=>"Bell Pepper","image"=>null,"digunakan_untuk"=>"Dimakan mentah, ditumis, atau jadi hiasan masakan.","fungsi"=>"Sangat kaya vitamin C, lebih banyak dari jeruk!","spesifikasi"=>"Bentuknya bulat agak kotak, ada merah, kuning, dan hijau.","fakta"=>"Paprika hijau adalah yang belum matang, merah dan kuning sudah matang."],
        ];

        $activities = [
            [
                'title' => 'Mengenal Buah-buahan Dasar',
                'desc' => 'Anak mengenal nama dan ciri buah yang sering ditemui sehari-hari.',
                'ages' => [1, 2, 3],
                'skills' => ['mengenal_benda', 'berbicara_sederhana'],
                'count' => 3,
                'status' => 'pending',
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
            [
                'title' => 'Mengenal Sayuran Hijau',
                'desc' => 'Anak mengenal nama dan ciri sayuran hijau yang sehat.',
                'ages' => [2, 3, 4, 5],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'kesehatan'],
                'count' => 3,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran Merah dan Oranye',
                'desc' => 'Anak mengenal sayuran berwarna merah dan oranye.',
                'ages' => [2, 3, 4, 5],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'mengenal_warna'],
                'count' => 4,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Bumbu Dapur',
                'desc' => 'Anak mengenal bumbu dapur yang sering dipakai memasak.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'mengenal_fungsi', 'pengetahuan_umum'],
                'count' => 3,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran Umbi',
                'desc' => 'Anak mengenal sayuran yang tumbuh di dalam tanah.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'mengenal_fungsi', 'mengenal_alam'],
                'count' => 4,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran Buah',
                'desc' => 'Anak mengenal sayuran yang sebenarnya adalah buah.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'pengetahuan_umum', 'mengenal_rasa'],
                'count' => 5,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran untuk Salad',
                'desc' => 'Anak mengenal sayuran yang biasa dimakan mentah.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'kesehatan', 'mengenal_rasa'],
                'count' => 5,
                'status' => 'pending',
            ],
            [
                'title' => 'Kumpulan Sayuran Populer',
                'desc' => 'Anak mengenal sayuran yang populer di Indonesia.',
                'ages' => [3, 4, 5, 6, 7],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'mengenal_fungsi'],
                'count' => 10,
                'status' => 'pending',
            ],
            [
                'title' => 'Ensiklopedia Sayuran',
                'desc' => 'Anak mengenal berbagai jenis sayuran lengkap dengan fakta menarik.',
                'ages' => [4, 5, 6, 7, 8],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'mengenal_fungsi', 'pengetahuan_umum'],
                'count' => 20,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran untuk Sup',
                'desc' => 'Anak mengenal sayuran yang biasa dimasak sup.',
                'ages' => [2, 3, 4, 5],
                'skills' => ['mengenal_benda', 'mengenal_fungsi', 'kesehatan'],
                'count' => 4,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran untuk Tumis',
                'desc' => 'Anak mengenal sayuran yang enak ditumis.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'mengenal_fungsi', 'mengenal_rasa'],
                'count' => 5,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran untuk Lalapan',
                'desc' => 'Anak mengenal sayuran yang biasa dimakan mentah.',
                'ages' => [2, 3, 4, 5],
                'skills' => ['mengenal_benda', 'mengenal_rasa', 'kesehatan'],
                'count' => 4,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran Kaya Vitamin',
                'desc' => 'Anak mengenal sayuran yang kaya vitamin untuk tubuh.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'kesehatan', 'pengetahuan_umum'],
                'count' => 6,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran dari Kebun',
                'desc' => 'Anak mengenal sayuran yang bisa ditanam di kebun.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'mengenal_alam', 'tanggung_jawab'],
                'count' => 6,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran Berwarna Cerah',
                'desc' => 'Anak mengenal sayuran yang berwarna cerah dan menarik.',
                'ages' => [2, 3, 4, 5],
                'skills' => ['mengenal_benda', 'mengenal_warna', 'berbicara_sederhana'],
                'count' => 5,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran dari Pasar',
                'desc' => 'Anak mengenal sayuran yang sering dibeli di pasar.',
                'ages' => [2, 3, 4, 5],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'lingkungan'],
                'count' => 6,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran untuk Anak',
                'desc' => 'Anak mengenal sayuran yang disukai anak-anak.',
                'ages' => [1, 2, 3],
                'skills' => ['mengenal_benda', 'berbicara_sederhana', 'kesehatan'],
                'count' => 4,
                'status' => 'pending',
            ],
            [
                'title' => 'Ensiklopedia Sayuran Lengkap',
                'desc' => 'Anak mengenal semua sayuran dengan fakta dan manfaat lengkap.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['mengenal_benda', 'mengenal_fungsi', 'pengetahuan_umum', 'kesehatan'],
                'count' => 20,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran untuk Bayi',
                'desc' => 'Anak mengenal sayuran yang aman untuk bayi makan.',
                'ages' => [1, 2, 3],
                'skills' => ['mengenal_benda', 'kesehatan', 'mengenal_rasa'],
                'count' => 3,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran Musim Hujan',
                'desc' => 'Anak mengenal sayuran yang tumbuh subur saat musim hujan.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'mengenal_alam', 'pengetahuan_umum'],
                'count' => 5,
                'status' => 'pending',
            ],
            [
                'title' => 'Mengenal Sayuran untuk Jus',
                'desc' => 'Anak mengenal sayuran yang bisa dibuat jus sehat.',
                'ages' => [3, 4, 5, 6],
                'skills' => ['mengenal_benda', 'kesehatan', 'mengenal_rasa'],
                'count' => 5,
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
