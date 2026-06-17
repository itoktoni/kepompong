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
         * artisan seed:generate --table-mode --tables=activities
         *
         */

        $dataTables = [
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"roles":[{"name":"Dokter","emoji":"\\ud83d\\udc68\\u200d\\u2695\\ufe0f","desc":"Memeriksa pasien dengan sabar dan menjelaskan dengan baik."},{"name":"Pasien","emoji":"\\ud83e\\uddd2","desc":"Menceritakan keluhan dengan jujur dan tidak takut."}],"pages":[{"num":1,"narrator":"Raka sakit perut. Ibu membawanya ke dokter.","dialog":[{"role":"Pasien","text":"Bu, aku takut ke dokter..."},{"role":"Ibu","text":"Tidak apa, Nak. Dokternya baik."}]},{"num":2,"narrator":"Dokter menyapa Raka dengan ramah.","dialog":[{"role":"Dokter","text":"Halo, Raka! Cerita dong, sakitnya di mana?"},{"role":"Pasien","text":"Perut saya sakit, Dok."}]},{"num":3,"narrator":"Dokter memeriksa perut Raka.","dialog":[{"role":"Dokter","text":"Ini cuma masuk angin. Nanti minum obat ya."},{"role":"Pasien","text":"Baik, Dok. Terima kasih!"}]}]}',
                'desc' => 'Anak kecil belajar tidak takut ke dokter.',
                'id' => 3,
                'image' => 'cover.png',
                'moral' => 'Jangan takut ke dokter. Komunikasi yang baik membantu proses penyembuhan.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berani_bicara","mengelola_marah"]',
                'slug' => 'dokter-pasien-kecil',
                'sort_order' => 2,
                'status' => 'approved',
                'title' => 'Dokter & Pasien Kecil',
                'type' => 'bermain_peran',
                'views' => 33,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"how":"Duduk berkelompok. Orang pertama menyebut kata baik. Orang berikutnya menyebut kata yang diawali huruf terakhir kata sebelumnya.","rules":["Duduk berkelompok 3-5 orang","Sebut kata baik bergiliran","Kata baru harus diawali huruf terakhir kata sebelumnya","Tidak boleh mengulang kata"]}',
                'desc' => 'Permainan kata untuk melatih kosakata positif.',
                'id' => 4,
                'image' => NULL,
                'moral' => 'Banyak kata baik yang bisa kita gunakan setiap hari.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["mendengarkan","berpikir_kreatif"]',
                'slug' => 'estafet-kata-baik',
                'sort_order' => 3,
                'status' => 'approved',
                'title' => 'Estafet Kata Baik',
                'type' => 'permainan',
                'views' => 5,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"script":"Halo semuanya! Namaku Adit. Hari ini aku mau cerita tentang hari pertamaku di sekolah baru. Aku sangat gugup. Tapi aku ingat pesan ibu, Jangan takut, kamu pasti bisa! Aku pun masuk ke kelas dengan senyuman. Ternyata, teman-teman sangat ramah. Sekarang aku punya banyak sahabat!","tips":["Berdiri tegak dan percaya diri","Bicara dengan jelas dan pelan","Gunakan ekspresi wajah","Tatap penonton"]}',
                'desc' => 'Monolog tentang keberanian di hari pertama sekolah.',
                'id' => 5,
                'image' => NULL,
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berani_bicara","berani_mencoba"]',
                'slug' => 'aku-bisa',
                'sort_order' => 4,
                'status' => 'approved',
                'title' => 'Aku Bisa!',
                'type' => 'monolog',
                'views' => 2,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"duration":"45 menit","difficulty":"Mudah","materials":["Kertas gambar besar","Crayon atau cat air","Pensil","Penghapus"],"steps":[{"title":"Siapkan Bahan","desc":"Ambil kertas gambar besar. Siapkan crayon atau cat air."},{"title":"Gambar Keluarga","desc":"Mulai gambar anggota keluarga satu per satu."},{"title":"Warnai","desc":"Warnai setiap anggota keluarga dengan warna favorit."},{"title":"Tambah Latar","desc":"Gambar latar belakang: rumah, taman, atau tempat favorit keluarga."}]}',
                'desc' => 'Buat lukisan keluarga menggunakan cat air atau crayon.',
                'id' => 6,
                'image' => 'https://images.unsplash.com/photo-1513364776144-60967b0f8000?w=400&h=600&fit=crop',
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","quality_time"]',
                'slug' => 'lukisan-keluarga',
                'sort_order' => 5,
                'status' => 'approved',
                'title' => 'Lukisan Keluarga',
                'type' => 'proyek_kreatif',
                'views' => 1,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[1,2,3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"lyrics":"\\ud83c\\udfb5 Satu dua tiga empat lima,\\\\nEnam tujuh delapan sembilan,\\\\nAyo kita berhitung bersama,\\\\nBelajar itu menyenangkan! \\ud83c\\udfb5","moves":["Angkat jari satu per satu","Hitung dengan jari kedua tangan","Bertepuk saat menyebut angka","Melompat kecil saat refrain"],"moral":"Belajar sambil bernyanyi membuat mudah diingat!"}',
                'desc' => 'Lagu sambil belajar berhitung 1-10.',
                'id' => 7,
                'image' => NULL,
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","rutin_belajar"]',
                'slug' => 'lagu-berhitung',
                'sort_order' => 6,
                'status' => 'approved',
                'title' => 'Lagu Berhitung',
                'type' => 'musik_gerak',
                'views' => 0,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"questions":[{"q":"Aku belang hitam oranye, buas dan suka sendirian. Siapa aku?","a":"Harimau!","hint":"Raja hutan yang punya loreng","emoji":"\\ud83d\\udc05"},{"q":"Aku bertanduk, suka makan rumput, dan menghasilkan susu. Siapa aku?","a":"Sapi!","hint":"Hewan ternak petani","emoji":"\\ud83d\\udc04"},{"q":"Aku punya sayap dan bisa terbang tinggi. Siapa aku?","a":"Burung!","hint":"Hewan yang bersarang di pohon","emoji":"\\ud83d\\udc26"}]}',
                'desc' => 'Teka-teki tentang hewan untuk melatih berpikir kreatif.',
                'id' => 8,
                'image' => NULL,
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","berani_mencoba"]',
                'slug' => 'teka-teki-hewan',
                'sort_order' => 7,
                'status' => 'approved',
                'title' => 'Teka-Teki Hewan',
                'type' => 'puzzle',
                'views' => 7,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"steps":["Ambil kertas dan pensil","Gambar wajahmu hari ini (senyum, sedih, marah, tenang]","Tulis 3 hal yang membuatmu senang","Tulis 1 hal yang membuatmu sedih","Peluk orang tuamu dan ceritakan"],"benefit":"Mengenali dan mengekspresikan perasaan dengan sehat."}',
                'desc' => 'Menulis dan menggambar perasaan hari ini.',
                'id' => 9,
                'image' => NULL,
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["mengenali_emosi","bersyukur"]',
                'slug' => 'jurnal-perasaan',
                'sort_order' => 8,
                'status' => 'approved',
                'title' => 'Jurnal Perasaan',
                'type' => 'mindfulness',
                'views' => 1,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"steps":["Bawa kantong kecil","Cari 5 daun berbeda bentuk","Cari 3 batu berbeda ukuran","Cari 1 bunga atau ranting","Kembali dan ceritakan apa yang ditemukan"],"observation":"Perhatikan warna, bentuk, dan tekstur setiap benda."}',
                'desc' => 'Petualangan mencari benda-benda menarik di alam.',
                'id' => 10,
                'image' => NULL,
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["olahraga_teratur","berimajinasi"]',
                'slug' => 'berburu-harta-karun-alam',
                'sort_order' => 9,
                'status' => 'approved',
                'title' => 'Berburu Harta Karun Alam',
                'type' => 'outdoor',
                'views' => 1,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"materials":["Botol plastik kecil","Baking soda","Cuka","Pewarna makanan","Sabun cair"],"steps":["Masukkan baking soda ke botol","Tambahkan pewarna dan sabun","Tuangkan cuka perlahan","Lihat reaksinya!"],"explanation":"Reaksi kimia antara baking soda (basa] dan cuka (asam] menghasilkan gas CO2 yang membuat busa."}',
                'desc' => 'Membuat gunung berapi mini dari baking soda.',
                'id' => 11,
                'image' => NULL,
                'moral' => NULL,
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","berani_mencoba"]',
                'slug' => 'eksperimen-gunung-berapi',
                'sort_order' => 10,
                'status' => 'approved',
                'title' => 'Eksperimen Gunung Berapi',
                'type' => 'ilmu_pengetahuan',
                'views' => 1,
            ],
            [
                'active' => 1,
                'agama' => '[]',
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => 'Halo Nama Saya',
                'data' => '{"pages":[{"num":1,"text":"\\"Lihat tubuhku yang anggun ini!\\" seru Marco dengan angkuh. \\"Aku berenang lebih cepat dari siapa pun di lautan ini!\\" Semua ikan di sekitarnya hanya terdiam mendengarnya."},{"num":2,"text":"Marco berenang kemana-mana dengan percaya diri tinggi. \\"Tidak ada yang bisa menandingi kecepatanku!\\" katanya sambil berbalik. Lina, ikan lumba-lumba yang bijaksana, hanya menggeleng melihat Marco."},{"num":3,"text":"\\"Marco, mengapa kau selalu menyombongkan diri?\\" tanya Lina dengan lembut. \\"Kecepatan bukan segalanya di lautan ini, Marco. Yang penting adalah kebaikan hati dan menghargai teman-temanmu.\\""},{"num":4,"text":"\\"Bajingan!\\" dengus Marco sambil berenang menjauh. \\"Kau hanya iri karena tidak secepat aku!\\" Lina terdiam sedih sementara Marco berenang dengan anggun menembus arus laut."},{"num":5,"text":"Di sebuah padang lamun, Marco bertemu Tuwru, kura-kura tua yang lambat dan bijaksana. \\"Hai Marco! Mau kemana dengan semangat seperti itu?\\" tanya Tuwru dengan senyum ramah."},{"num":6,"text":"\\"Aku mencari korban untuk diajak berlomba!\\" ejek Marco. \\"Kau, Kura-kura dungu! Coba kejar aku jika berani!\\" Semua ikan di sekitar mereka menahan napas menunggu jawaban Tuwru."},{"num":7,"text":"Tuwru tersenyum tenang dan menjawab, \\"Aku terima tantanganmu, Marco. Kita berlomba dari karang merah sampai gua biru esok pagi.\\" Marco tertawa keras. \\"Kau pasti akan kalah, !\\""},{"num":8,"text":"Kabar lomba Marco melawan Tuwru menyebar ke seluruh lautan. Semua penghuni laut penasaran. \\"Apakah kura-kura lambat bisa mengalahkan marlin paling cepat?\\" bisik mereka dengan ragu."},{"num":9,"text":"Pagi tiba dan semua ikan berkumpul di garis start. Lina berdiri sebagai wasit. \\"Persiapan!\\" teriaknya. Marco dengan yakin meregangkan siripnya sementara Tuwru menutup matanya dengan tenang."},{"num":10,"text":"\\"Pergi!\\" teriak Lina. Marco melesat kencang seperti anak panah, meninggalkan jejak busa. Tuwru hanya berenang perlahan dengan langkah pasti. Marco tertawa. \\"Sampah! Aku sudah menang!\\""},{"num":11,"text":"Marco terus berenang dengan penuh percaya diri. Ia berbalik dan melihat Tuwru yang tampak seperti titik kecil di kejauhan. \\"Bukan lawan!\\" tukasnya sambil mengerjai karang-karang besar."},{"num":12,"text":"Di tengah perjalanan, Marco merasa lelah dan bosan. Ia berhenti di gua bawah laut untuk istirahat. \\"Tuwru pasti masih jauh di belakang,\\" gumamnya sambil tertidur di pasir putih yang lembut."},{"num":13,"text":"Sementara Marco tertidur nyenyak, Tuwru terus berenang melewati karang, menjelajahi gua gelap, dan melewati padang lamun dengan sabar. Satu per satu ikan ikut mendorong Tuwru maju."},{"num":14,"text":"Marco bangun dan berlari ke garis finish. Ia terkejut melihat semua ikan bersorak untuk Tuwru. \\"Selamat, Tuwru!\\" teriak mereka. Marco berdiri tercengang. \\"Apa yang terjadi?\\" tanyanya dengan lemas."},{"num":15,"text":"Guru, paus bijaksana, menghampiri Marco. \\"Kesombonganmu membuatmu lengah dan tertidur. Sementara Tuwru tekun melangkah tanpa henti.\\" Marco menunduk malu. \\"Maafkan aku, Tuwru,\\" bisiknya."}]}',
                'desc' => 'Seekor ikan marlin bernama Marco yang sombong merasa dialah yang paling cepat di lautan, menantang semua penghuni laut untuk berlomba, dan akhirnya belajar pelajaran penting tentang kerendahan hati.',
                'id' => 64,
                'image' => 'cover.png',
                'moral' => 'Kesombongan membuat kita lengah dan tidak menghargai orang lain. Kerja keras, ketekunan, dan kerendahan hati lebih berharga daripada kecepatan semata. Selalu hargai setiap makhluk, karena setiap kebaikan akan kembali kepada kita.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => 'A 16-panel comic page storyboard, single image with a 4x4 panel grid.

Title: Marco Marlin dan Lomba Besar Lautan
Description: Seekor ikan marlin bernama Marco yang sombong merasa dialah yang paling cepat di lautan, menantang semua penghuni laut untuk berlomba, dan akhirnya belajar pelajaran penting tentang kerendahan hati.
Moral: Kesombongan membuat kita lengah dan tidak menghargai orang lain. Kerja keras, ketekunan, dan kerendahan hati lebih berharga daripada kecepatan semata. Selalu hargai setiap makhluk, karena setiap kebaikan akan kembali kepada kita.

Each panel is an illustration for the story:

Panel 1 (cover]: Title "Marco Marlin dan Lomba Besar Lautan" centered, colorful kid-friendly illustration representing the story theme.
Panel 1: "Lihat tubuhku yang anggun ini!" seru Marco dengan angkuh. "Aku berenang lebih cepat dari siapa pun di lautan ini!" Semua ikan di sekitarnya hanya terdiam mendengarnya.
Panel 2: Marco berenang kemana-mana dengan percaya diri tinggi. "Tidak ada yang bisa menandingi kecepatanku!" katanya sambil berbalik. Lina, ikan lumba-lumba yang bijaksana, hanya menggeleng melihat Marco.
Panel 3: "Marco, mengapa kau selalu menyombongkan diri?" tanya Lina dengan lembut. "Kecepatan bukan segalanya di lautan ini, Marco. Yang penting adalah kebaikan hati dan menghargai teman-temanmu."
Panel 4: "Bajingan!" dengus Marco sambil berenang menjauh. "Kau hanya iri karena tidak secepat aku!" Lina terdiam sedih sementara Marco berenang dengan anggun menembus arus laut.
Panel 5: Di sebuah padang lamun, Marco bertemu Tuwru, kura-kura tua yang lambat dan bijaksana. "Hai Marco! Mau kemana dengan semangat seperti itu?" tanya Tuwru dengan senyum ramah.
Panel 6: "Aku mencari korban untuk diajak berlomba!" ejek Marco. "Kau, Kura-kura dungu! Coba kejar aku jika berani!" Semua ikan di sekitar mereka menahan napas menunggu jawaban Tuwru.
Panel 7: Tuwru tersenyum tenang dan menjawab, "Aku terima tantanganmu, Marco. Kita berlomba dari karang merah sampai gua biru esok pagi." Marco tertawa keras. "Kau pasti akan kalah, !"
Panel 8: Kabar lomba Marco melawan Tuwru menyebar ke seluruh lautan. Semua penghuni laut penasaran. "Apakah kura-kura lambat bisa mengalahkan marlin paling cepat?" bisik mereka dengan ragu.
Panel 9: Pagi tiba dan semua ikan berkumpul di garis start. Lina berdiri sebagai wasit. "Persiapan!" teriaknya. Marco dengan yakin meregangkan siripnya sementara Tuwru menutup matanya dengan tenang.
Panel 10: "Pergi!" teriak Lina. Marco melesat kencang seperti anak panah, meninggalkan jejak busa. Tuwru hanya berenang perlahan dengan langkah pasti. Marco tertawa. "Sampah! Aku sudah menang!"
Panel 11: Marco terus berenang dengan penuh percaya diri. Ia berbalik dan melihat Tuwru yang tampak seperti titik kecil di kejauhan. "Bukan lawan!" tukasnya sambil mengerjai karang-karang besar.
Panel 12: Di tengah perjalanan, Marco merasa lelah dan bosan. Ia berhenti di gua bawah laut untuk istirahat. "Tuwru pasti masih jauh di belakang," gumamnya sambil tertidur di pasir putih yang lembut.
Panel 13: Sementara Marco tertidur nyenyak, Tuwru terus berenang melewati karang, menjelajahi gua gelap, dan melewati padang lamun dengan sabar. Satu per satu ikan ikut mendorong Tuwru maju.
Panel 14: Marco bangun dan berlari ke garis finish. Ia terkejut melihat semua ikan bersorak untuk Tuwru. "Selamat, Tuwru!" teriak mereka. Marco berdiri tercengang. "Apa yang terjadi?" tanyanya dengan lemas.
Panel 15: Guru, paus bijaksana, menghampiri Marco. "Kesombonganmu membuatmu lengah dan tertidur. Sementara Tuwru tekun melangkah tanpa henti." Marco menunduk malu. "Maafkan aku, Tuwru," bisiknya.

Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.

Rules:
- Panel 1 is the cover with title text centered
- cover title is not to big and small- No written text in other panels except cover
- No speech bubbles allowed
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No Page number
- Funny expressions, clear visual storytelling
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel
',
                'skills' => '[]',
                'slug' => 'marco-marlin-dan-lomba-besar-lautan',
                'sort_order' => 0,
                'status' => 'approved',
                'title' => 'Marco Marlin dan Lomba Besar Lautan',
                'type' => 'storytelling',
                'views' => 50,
            ],
            [
                'active' => 1,
                'agama' => '[]',
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => 'Halo Nama Saya',
                'data' => '{"pages":[{"num":1,"text":"Di tepi sungai yang tenang, tinggallah kura-kura Tobi dengan tempurung hijau kesayangannya. Tobi bergerak lambat tetapi hatinya tulus dan hangat. Ia selalu tersenyum ramah kepada siapa pun yang ditemuinya."},{"num":2,"text":"\\"Hei, Kura-kura Tobi! Kau bergerak seperti siput,\\" ejek Bruno suatu pagi. \\"Dengan selambat itu, kau tidak akan pernah mencapai tempat yang kau inginkan!\\""},{"num":3,"text":"Tobi tersenyum lembut dan menjawab, \\"Aku memang lambat, Bruno. Tapi aku tidak pernah berhenti berusaha. Bagaimana kalau kita adu kecepatan menuju pohon besar di bukit?\\""},{"num":4,"text":"\\"Ha! Tantangan diterima,\\" kata Bruno dengan suara besar. \\"Aku akan menang mudah! Kau akan malu sekali, Tobi.\\" Seluruh hutan mendengar tantangan seru mereka hari itu."},{"num":5,"text":"Perlombaan dimulai! Bruno berlari kencang seperti angin, melewati bunga-bunga dan semak-semak. Tobi melangkah pelan tetapi pasti, mengikuti jalan yang telah ditentukannya."},{"num":6,"text":"Bruno berlari dengan penuh semangat di awal jalur. Namun setelah beberapa saat, ia melihat pohon mangga berbuah matang. \\"Wah, mangga! Aku sempat santai sebentar,\\" pikir Bruno. Ia duduk santai memakan mangga."},{"num":7,"text":"Bruno tertidur pulas di bawah pohon mangga. Matahari bersinar terang sementara Tobi terus melangkah maju. Belalang hijau ikut berjalan di samping Tobi seolah menyemangati temannya yang lambat."},{"num":8,"text":"\\"Bruno pasti sudah jauh di depan,\\" gumam Tobi saat beristirahat sebentar. Seekor kupu-kupu indah hinggap di tempurungnya. \\"Tapi aku tidak akan menyerah,\\" lanjut Tobi dengan senyum penuh keyakinan."},{"num":9,"text":"Di tengah perjalanan, Tobi menemukan yang terjebak di lumpur. \\"Tolong aku, Tobi!\\" seru kecil itu. Tobi segera membantu dengan sabar hingga itu bebas dari lumpur."},{"num":10,"text":"\\"Terima kasih, Tobi!\\" kata dengan mata berkaca-kaca. \\"Semoga kau menang dalam perlombaanmu.\\" Tobi mengangguk agradecido dan melanjutkan perjalanannya dengan hati lega."},{"num":11,"text":"Bruno terbangun dari tidurnya. Matahari sudah mulai terbenam! \\"Aduh, aku tertidur!\\" Ia berlari secepat kilat menuju garis finish. Sayangnya Bruno salah jalur dan berlari ke arah yang salah."},{"num":12,"text":"Tobi tiba di garis finish yang pohon besar dengan tenang. Burung-burung berkicau gembira menyambut kedatangan Tobi. \\"Selamat, Tobi! Kau pemenangnya!\\" seru mereka dengan riang."},{"num":13,"text":"Bruno datang tidak lama kemudian, terengah-engah dan berkeringat. Ia melihat Tobi sudah berdiri di garis finish. \\"Bagaimana bisa? Aku berlari lebih cepat!\\" kata Bruno dengan wajah sedih dan malu."},{"num":14,"text":"\\"Kecepatan bukan segalanya, Bruno,\\" kata Tobi dengan bijak. \\"Aku menang karena tidak berhenti berusaha, tidak mudah tertidur, dan selalu membantu yang membutuhkan.\\" Bruno terdiam dan menunduk dengan penyesalan."},{"num":15,"text":"Bruno memeluk Tobi erat dan berkata, \\"Maafkan aku, Tobi. Aku terlalu sombong dan meremehkanmu. Apakah kau mau berteman denganku?\\" Tobi tersenyum dan menjawab, \\"Tentu saja, Bruno! Kebriendship kita akan lebih berharga dari kemenangan manapun.\\""}]}',
                'desc' => 'Kisah tentang kelinci Bruno yang sombong dan kura-kura Tobi yang sederhana, mereka bersaing dalam sebuah perlombaan yang mengajarkan bahwa kesombongan membawa kegagalan.',
                'id' => 65,
                'image' => 'cover.png',
                'moral' => 'Kesombongan membuat kita jatuh, sedangkan kerendahan hati dan kerja keras membawa keberhasilan. Jangan pernah meremehkan orang lain hanya karena mereka berbeda.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => 'A 16-panel comic page storyboard, single image with a 4x4 panel grid.

Title: Petualangan Kelinci Bruno dan Kura-kura Tobi
Description: Kisah tentang kelinci Bruno yang sombong dan kura-kura Tobi yang sederhana, mereka bersaing dalam sebuah perlombaan yang mengajarkan bahwa kesombongan membawa kegagalan.
Moral: Kesombongan membuat kita jatuh, sedangkan kerendahan hati dan kerja keras membawa keberhasilan. Jangan pernah meremehkan orang lain hanya karena mereka berbeda.

Each panel is an illustration for the story:

Panel 1 (cover]: Title "Petualangan Kelinci Bruno dan Kura-kura Tobi" centered, colorful kid-friendly illustration representing the story theme.
Panel 1: Di tepi sungai yang tenang, tinggallah kura-kura Tobi dengan tempurung hijau kesayangannya. Tobi bergerak lambat tetapi hatinya tulus dan hangat. Ia selalu tersenyum ramah kepada siapa pun yang ditemuinya.
Panel 2: "Hei, Kura-kura Tobi! Kau bergerak seperti siput," ejek Bruno suatu pagi. "Dengan selambat itu, kau tidak akan pernah mencapai tempat yang kau inginkan!"
Panel 3: Tobi tersenyum lembut dan menjawab, "Aku memang lambat, Bruno. Tapi aku tidak pernah berhenti berusaha. Bagaimana kalau kita adu kecepatan menuju pohon besar di bukit?"
Panel 4: "Ha! Tantangan diterima," kata Bruno dengan suara besar. "Aku akan menang mudah! Kau akan malu sekali, Tobi." Seluruh hutan mendengar tantangan seru mereka hari itu.
Panel 5: Perlombaan dimulai! Bruno berlari kencang seperti angin, melewati bunga-bunga dan semak-semak. Tobi melangkah pelan tetapi pasti, mengikuti jalan yang telah ditentukannya.
Panel 6: Bruno berlari dengan penuh semangat di awal jalur. Namun setelah beberapa saat, ia melihat pohon mangga berbuah matang. "Wah, mangga! Aku sempat santai sebentar," pikir Bruno. Ia duduk santai memakan mangga.
Panel 7: Bruno tertidur pulas di bawah pohon mangga. Matahari bersinar terang sementara Tobi terus melangkah maju. Belalang hijau ikut berjalan di samping Tobi seolah menyemangati temannya yang lambat.
Panel 8: "Bruno pasti sudah jauh di depan," gumam Tobi saat beristirahat sebentar. Seekor kupu-kupu indah hinggap di tempurungnya. "Tapi aku tidak akan menyerah," lanjut Tobi dengan senyum penuh keyakinan.
Panel 9: Di tengah perjalanan, Tobi menemukan yang terjebak di lumpur. "Tolong aku, Tobi!" seru kecil itu. Tobi segera membantu dengan sabar hingga itu bebas dari lumpur.
Panel 10: "Terima kasih, Tobi!" kata dengan mata berkaca-kaca. "Semoga kau menang dalam perlombaanmu." Tobi mengangguk agradecido dan melanjutkan perjalanannya dengan hati lega.
Panel 11: Bruno terbangun dari tidurnya. Matahari sudah mulai terbenam! "Aduh, aku tertidur!" Ia berlari secepat kilat menuju garis finish. Sayangnya Bruno salah jalur dan berlari ke arah yang salah.
Panel 12: Tobi tiba di garis finish yang pohon besar dengan tenang. Burung-burung berkicau gembira menyambut kedatangan Tobi. "Selamat, Tobi! Kau pemenangnya!" seru mereka dengan riang.
Panel 13: Bruno datang tidak lama kemudian, terengah-engah dan berkeringat. Ia melihat Tobi sudah berdiri di garis finish. "Bagaimana bisa? Aku berlari lebih cepat!" kata Bruno dengan wajah sedih dan malu.
Panel 14: "Kecepatan bukan segalanya, Bruno," kata Tobi dengan bijak. "Aku menang karena tidak berhenti berusaha, tidak mudah tertidur, dan selalu membantu yang membutuhkan." Bruno terdiam dan menunduk dengan penyesalan.
Panel 15: Bruno memeluk Tobi erat dan berkata, "Maafkan aku, Tobi. Aku terlalu sombong dan meremehkanmu. Apakah kau mau berteman denganku?" Tobi tersenyum dan menjawab, "Tentu saja, Bruno! Kebriendship kita akan lebih berharga dari kemenangan manapun."

Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.

Rules:
- Panel 1 is the cover with title text centered
- cover title is not to big and small- No written text in other panels except cover
- No speech bubbles allowed
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No Page number
- Funny expressions, clear visual storytelling
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel
',
                'skills' => '[]',
                'slug' => 'petualangan-kelinci-bruno-dan-kura-kura-tobi',
                'sort_order' => 0,
                'status' => 'pending',
                'title' => 'Petualangan Kelinci Bruno dan Kura-kura Tobi',
                'type' => 'storytelling',
                'views' => 20,
            ],
            [
                'active' => 1,
                'agama' => '[]',
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => 'Halo Nama Saya',
                'data' => '{"pages":[{"num":1,"text":"\\"Selamat pagi, Kancil,\\" sapa Pak Burung Hantu Wisnu dari pohon tua. \\"Kabar buruk, sungai besar kita mulai kering. Hewan-hewan sangat membutuhkan air bersih untuk minum.\\""},{"num":2,"text":"\\"Jangan khawatir, Pak Wisnu,\\" jawab Kancil dengan senyum ceria. \\"Saya akan mencari tahu apa yang menyebabkan sungai kering. Mari kita selidiki bersama ke tepi sungai.\\""},{"num":3,"text":"Setibanya di tepi sungai, mereka menemukan Mbak Rajungan Cantik sedang menangis dengan sedih. \\"Bebek besar telah membangun bendungan besar dari batu dan ranting,\\" katanya terisak."},{"num":4,"text":"\\"Bendungan itu menghalangi aliran air bersih ke desa kita,\\" lanjut Mbak Rajungan denganNada khawatir. \\"Ikan-ikan kecil di hilir sungai mulai kehabisan napas dan banyak hewan kehausan.\\""},{"num":5,"text":"Kancil mengerutkan dahinya sambil berpikir keras. Tiba-tiba matanya bersinar terang. \\"Saya punya ide yang luar biasa! Kita tidak perlu merusak bendungan itu,\\" serunya penuh semangat."},{"num":6,"text":"\\"Kita akan membuat saluran air baru yang mengalir melewati bukit kecil,\\" jelas Kancil dengan penuh percaya diri sambil menggambar di tanah dengan rantingnya."},{"num":7,"text":"\\"Ide yang brillian!\\" puji Pak Burung Hantu Wisnu dengan kagum. \\"Tapi kita butuh bantuan lebih banyak teman untuk menyelesaikan proyek besar ini.\\""},{"num":8,"text":"Kaka Tupai Lincah muncul melompat dari pohon ke pohon dengan gesit. \\"Saya mendengar tentang masalah kalian! Saya siap membantu menggali tanah dengan kukuku yang tajam,\\" kata Tupai dengan semangat."},{"num":9,"text":"Empat sahabat bekerja keras bersama di bawah terik matahari. Kancil kerja sama dengan bijaksana sementara teman-temannya bergantian menggali tanah dengan penuh dedikasi."},{"num":10,"text":"Setelah beberapa jam bekerja tanpa henti, mereka berhasil membuat parit besar yang menghubungkan sungai ke desa. Air jernih mulai mengalir melalui saluran baru dengan alegria."},{"num":11,"text":"\\"Berhasil! Airnya mengalir dengan indah,\\" teriak Mbak Rajungan kegirangan sambil bertepuk tangan. Hewan-hewan desa berdatangan melihat keajaiban yang dibuat oleh keempat sahabat."},{"num":12,"text":"Bebek Besar datang dengan perasaan bersalah. \\"Maafkan saya, teman-teman. Saya tidak bermaksud merugikan kalian,\\" pintanya dengan tulus. \\"Bendungan saya justru membuat saya sendiri kehausan.\\""},{"num":13,"text":"\\"Tidak apa-apa, Bebek,\\" kata Kancil dengan hangat. \\"Yang penting kita semua belajar bersama. Penggunaan kecerdasan yang baik adalah untuk kepentingan bersama, bukan untuk kepentingan sendiri.\\""},{"num":14,"text":"Hutan Sejahtera kembali ramai dengan tawa dan kebahagiaan. Pak Burung Hantu Wisnu tersenyum bangga melihat persahabatan yang terbentuk dari tantangan bersama."},{"num":15,"text":"\\"Kecerdasan yang sejati adalah ketika kita,\\" pesan Pak Burung Hantu kepada semua yang hadir. Kancil tersenyum, ia belajar bahwa kebaikan hati dan kecerdikan berjalan beriringan untuk menciptakan kebahagiaan bersama."}]}',
                'desc' => 'Kancil si mousedeer yang cerdik harus membantu teman-temannya mengatasi kekeringan di hutan dengan kecerdikannya.',
                'id' => 66,
                'image' => 'cover.png',
                'moral' => 'Kepintaran sejati adalah menggunakan kecerdasan untuk membantu orang lain, bukan untuk mencelakakan mereka.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => 'A 16-panel comic page storyboard, single image with a 4x4 panel grid.

Title: Petualangan Kancil Cerdik di Hutan Sejahtera
Description: Kancil si mousedeer yang cerdik harus membantu teman-temannya mengatasi kekeringan di hutan dengan kecerdikannya.
Moral: Kepintaran sejati adalah menggunakan kecerdasan untuk membantu orang lain, bukan untuk mencelakakan mereka.

Each panel is an illustration for the story:

Panel 1 (cover]: Title "Petualangan Kancil Cerdik di Hutan Sejahtera" centered, colorful kid-friendly illustration representing the story theme.
Panel 1: "Selamat pagi, Kancil," sapa Pak Burung Hantu Wisnu dari pohon tua. "Kabar buruk, sungai besar kita mulai kering. Hewan-hewan sangat membutuhkan air bersih untuk minum."
Panel 2: "Jangan khawatir, Pak Wisnu," jawab Kancil dengan senyum ceria. "Saya akan mencari tahu apa yang menyebabkan sungai kering. Mari kita selidiki bersama ke tepi sungai."
Panel 3: Setibanya di tepi sungai, mereka menemukan Mbak Rajungan Cantik sedang menangis dengan sedih. "Bebek besar telah membangun bendungan besar dari batu dan ranting," katanya terisak.
Panel 4: "Bendungan itu menghalangi aliran air bersih ke desa kita," lanjut Mbak Rajungan denganNada khawatir. "Ikan-ikan kecil di hilir sungai mulai kehabisan napas dan banyak hewan kehausan."
Panel 5: Kancil mengerutkan dahinya sambil berpikir keras. Tiba-tiba matanya bersinar terang. "Saya punya ide yang luar biasa! Kita tidak perlu merusak bendungan itu," serunya penuh semangat.
Panel 6: "Kita akan membuat saluran air baru yang mengalir melewati bukit kecil," jelas Kancil dengan penuh percaya diri sambil menggambar di tanah dengan rantingnya.
Panel 7: "Ide yang brillian!" puji Pak Burung Hantu Wisnu dengan kagum. "Tapi kita butuh bantuan lebih banyak teman untuk menyelesaikan proyek besar ini."
Panel 8: Kaka Tupai Lincah muncul melompat dari pohon ke pohon dengan gesit. "Saya mendengar tentang masalah kalian! Saya siap membantu menggali tanah dengan kukuku yang tajam," kata Tupai dengan semangat.
Panel 9: Empat sahabat bekerja keras bersama di bawah terik matahari. Kancil kerja sama dengan bijaksana sementara teman-temannya bergantian menggali tanah dengan penuh dedikasi.
Panel 10: Setelah beberapa jam bekerja tanpa henti, mereka berhasil membuat parit besar yang menghubungkan sungai ke desa. Air jernih mulai mengalir melalui saluran baru dengan alegria.
Panel 11: "Berhasil! Airnya mengalir dengan indah," teriak Mbak Rajungan kegirangan sambil bertepuk tangan. Hewan-hewan desa berdatangan melihat keajaiban yang dibuat oleh keempat sahabat.
Panel 12: Bebek Besar datang dengan perasaan bersalah. "Maafkan saya, teman-teman. Saya tidak bermaksud merugikan kalian," pintanya dengan tulus. "Bendungan saya justru membuat saya sendiri kehausan."
Panel 13: "Tidak apa-apa, Bebek," kata Kancil dengan hangat. "Yang penting kita semua belajar bersama. Penggunaan kecerdasan yang baik adalah untuk kepentingan bersama, bukan untuk kepentingan sendiri."
Panel 14: Hutan Sejahtera kembali ramai dengan tawa dan kebahagiaan. Pak Burung Hantu Wisnu tersenyum bangga melihat persahabatan yang terbentuk dari tantangan bersama.
Panel 15: "Kecerdasan yang sejati adalah ketika kita," pesan Pak Burung Hantu kepada semua yang hadir. Kancil tersenyum, ia belajar bahwa kebaikan hati dan kecerdikan berjalan beriringan untuk menciptakan kebahagiaan bersama.

Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.

Rules:
- Panel 1 is the cover with title text centered
- cover title is not to big and small- No written text in other panels except cover
- No speech bubbles allowed
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No Page number
- Funny expressions, clear visual storytelling
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel
',
                'skills' => '[]',
                'slug' => 'petualangan-kancil-cerdik-di-hutan-sejahtera',
                'sort_order' => 0,
                'status' => 'approved',
                'title' => 'Petualangan Kancil Cerdik di Hutan Sejahtera',
                'type' => 'storytelling',
                'views' => 21,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"questions":[{"clue":"Aku punya telinga panjang dan suka makan wortel. Siapa aku?","answer":"Kelinci!","hint":"Hewan yang suka melompat-lompat","emoji":"\\ud83d\\udc07"},{"clue":"Aku berwarna kuning dan bentuknya bulat. Aku ada di langit saat pagi hari. Siapa aku?","answer":"Matahari!","hint":"Benda langit yang memberi cahaya dan kehangatan","emoji":"\\u2600\\ufe0f"},{"clue":"Aku punya belalai panjang dan badan besar. Siapa aku?","answer":"Gajah!","hint":"Hewan darat terbesar di dunia","emoji":"\\ud83d\\udc18"},{"clue":"Aku bisa terbang dan punya sayap berwarna-warni. Aku suka hinggap di bunga. Siapa aku?","answer":"Kupu-kupu!","hint":"Serangga indah yang berasal dari ulat","emoji":"\\ud83e\\udd8b"},{"clue":"Aku hijau dan suka melompat dari daun ke daun. Bunyiku ribut di malam hari. Siapa aku?","answer":"Katak!","hint":"Amfibi yang hidup di air dan di darat","emoji":"\\ud83d\\udc38"}]}',
                'desc' => 'Tebak-tebakan seru tentang hewan dan benda sekitar.',
                'id' => 67,
                'image' => NULL,
                'moral' => 'Belajar mengenal ciri-ciri makhluk hidup dan benda di sekitar kita.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","berani_mencoba"]',
                'slug' => 'tebak-hewan-lucu',
                'sort_order' => 1,
                'status' => 'approved',
                'title' => 'Tebak Hewan Lucu',
                'type' => 'tebak_teakan',
                'views' => 12,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"questions":[{"clue":"Aku punya kaki tapi tidak bisa berjalan. Aku ada di kamar tidurmu. Apa aku?","answer":"Tempat tidur!","hint":"Kamu tidur di atas aku setiap malam","emoji":"\\ud83d\\udecf"},{"clue":"Aku punya banyak gigi tapi tidak bisa mengunyah. Aku menyisir rambutmu setiap pagi. Apa aku?","answer":"Sisir!","hint":"Alat untuk merapikan rambut","emoji":"\\ud83e\\udee5"},{"clue":"Aku bisa berbunyi keras tapi tidak punya mulut. Aku membangunkanmu di pagi hari. Apa aku?","answer":"Alarm!","hint":"Jam yang bisa berbunyi","emoji":"\\u23f0"},{"clue":"Aku punya tangan tapi tidak bisa memegang. Aku menunjukkan waktu. Apa aku?","answer":"Jam!","hint":"Alat penunjuk waktu","emoji":"\\ud83d\\udd70\\ufe0f"}]}',
                'desc' => 'Tebak-tebakan benda sehari-hari yang melatih logika.',
                'id' => 68,
                'image' => NULL,
                'moral' => 'Belajar berpikir kreatif dan mengenali fungsi benda di sekitar kita.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","mendengarkan"]',
                'slug' => 'tebak-benda-sehari-hari',
                'sort_order' => 2,
                'status' => 'approved',
                'title' => 'Tebak Benda Sehari-hari',
                'type' => 'tebak_teakan',
                'views' => 8,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"how":"Permainan tangan yang menyenangkan untuk melatih koordinasi dan konsentrasi.","rules":["Duduk berhadapan dengan teman","Ikuti gerakan dengan tepat","Ucapkan kata-kata sambil bergerak","Tertawa dan bersenang-senang!"],"moves":["Tepuk tangan sendiri","Tepuk tangan teman","Tepuk tangan kanan-kiri bergantian","Tepuk tangan cepat","Tepuk tangan pelan"],"lyrics":"Tepuk tangan satu dua tiga,\\\\nKiri kanan kiri kanan,\\\\nSatu dua tiga empat lima,\\\\nAyo kita bermain bersama!"}',
                'desc' => 'Permainan tepuk tangan yang melatih koordinasi dan ritme.',
                'id' => 69,
                'image' => NULL,
                'moral' => 'Bermain bersama teman membuat kita lebih kompak dan gembira.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["mendengarkan","berpikir_kreatif"]',
                'slug' => 'tepuk-tangan-seru',
                'sort_order' => 1,
                'status' => 'approved',
                'title' => 'Tepuk Tangan Seru',
                'type' => 'permainan_tangan',
                'views' => 15,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"how":"Permainan jari untuk melatih motorik halus dan kreativitas.","rules":["Gunakan kedua tangan","Ikuti setiap langkah dengan pelan","Bisa dimainkan sendiri atau berkelompok","Kreasikan gerakanmu sendiri!"],"moves":["Buaya: kedua tangan saling menggigit","Kupu-kupu: kedua tangan saling mengepak","Bunga: jari-jari mekar seperti kelopak","Kelinci: dua jari tegak seperti telinga","Burung: kedua tangan mengepak seperti sayap"]}',
                'desc' => 'Permainan jari kreatif membentuk berbagai hewan.',
                'id' => 70,
                'image' => NULL,
                'moral' => 'Tangan kita bisa membuat banyak bentuk menakjubkan jika kita berkreasi.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","berani_mencoba"]',
                'slug' => 'jari-jari-ajaib',
                'sort_order' => 2,
                'status' => 'approved',
                'title' => 'Jari-jari Ajaib',
                'type' => 'permainan_tangan',
                'views' => 9,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"exercises":[{"title":"Hitung Mundur","instruction":"Hitung mundur dari 20 ke 1, tapi setiap angka kelipatan 3 ganti dengan tepuk tangan!","answer":"20, 19, (tepuk), 17, 16, (tepuk), 14, ...","type":"hitung"},{"title":"Balik Kata","instruction":"Katakan kata ini secara terbalik: KUCING","answer":"GNUCIUK","type":"kata"},{"title":"Lanjutkan Pola","instruction":"Lanjutkan pola ini: 2, 4, 6, 8, ...","answer":"10, 12, 14, ...","type":"pola"},{"title":"Bedakan Gambar","instruction":"Bayangkan 3 buah apel dan 5 buah jeruk. Berapa total semua buah?","answer":"8 buah","type":"visual"},{"title":"Tebak Urutan","instruction":"Ibu membeli susu, roti, dan telur. Ibu memakai sepatu terlebih dahulu. Apa yang ibu beli pertama?","answer":"Susu (karena susu disebut pertama)","type":"logika"}]}',
                'desc' => 'Latihan otak seru untuk melatih konsentrasi dan daya ingat.',
                'id' => 71,
                'image' => NULL,
                'moral' => 'Melatih otak setiap hari membuat kita semakin pintar dan fokus.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","rutin_belajar"]',
                'slug' => 'tantangan-otak-cerdas',
                'sort_order' => 1,
                'status' => 'approved',
                'title' => 'Tantangan Otak Cerdas',
                'type' => 'latihan_otak',
                'views' => 18,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"exercises":[{"title":"Ingat dan Sebut","instruction":"Lihat benda di sekitarmu selama 30 detik, tutup mata, sebutkan 5 benda yang kamu ingat!","answer":"Tergantung benda di sekitarmu","type":"ingatan"},{"title":"Hitung Cepat","instruction":"Berapa hasil dari 7 + 8 - 3 + 2?","answer":"14","type":"hitung"},{"title":"Kata Berlawanan","instruction":"Sebutkan lawan kata dari: GELAP, TINGGI, CEPAT","answer":"Terang, Rendah, Lambat","type":"kata"},{"title":"Susun Kalimat","instruction":"Susun kata-kata ini menjadi kalimat yang benar: pergi / ke / aku / sekolah","answer":"Aku pergi ke sekolah.","type":"bahasa"},{"title":"Tebak Angka","instruction":"Aku adalah angka. Jika ditambah 5 hasilnya 12. Siapa aku?","answer":"7","type":"logika"}]}',
                'desc' => 'Latihan otak tingkat lanjut untuk anak yang suka tantangan.',
                'id' => 72,
                'image' => NULL,
                'moral' => 'Semakin sering berlatih, semakin tajam otak kita.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","mendengarkan"]',
                'slug' => 'otak-super-lanjutan',
                'sort_order' => 2,
                'status' => 'approved',
                'title' => 'Otak Super Lanjutan',
                'type' => 'latihan_otak',
                'views' => 11,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"questions":[{"clue":"Aku punya daun hijau dan batang keras. Aku memberikan udara segar. Siapa aku?","answer":"Pohon!","hint":"Makhluk hidup yang berdiri tegak di tanah","emoji":"\\ud83c\\udf33"},{"clue":"Aku jatuh dari langit dan basah. Aku membuat tanaman tumbuh. Apa aku?","answer":"Hujan!","hint":"Cuaca yang membuat kita butuh payung","emoji":"\\ud83c\\udf27"},{"clue":"Aku berwarna merah dan manis. Banyak anak suka memakanku. Apa aku?","answer":"Apel!","hint":"Buah yang sering dijadikan jus","emoji":"\\ud83c\\udf4e"}]}',
                'desc' => 'Tebak-tebakan tentang alam dan tumbuhan.',
                'id' => 73,
                'image' => NULL,
                'moral' => 'Mengenal alam membantu kita mencintai lingkungan.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","berimajinasi"]',
                'slug' => 'tebak-alam-dan-tumbuhan',
                'sort_order' => 3,
                'status' => 'approved',
                'title' => 'Tebak Alam dan Tumbuhan',
                'type' => 'tebak_teakan',
                'views' => 6,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"how":"Permainan simon kata versi tangan yang seru dan menantang.","rules":["Satu orang jadi pemimpin","Pemain lain harus mengikuti gerakan tangan pemimpin","Hanya ikuti gerakan yang diawali kata Simon kata","Jika salah, ganti jadi pemimpin"],"moves":["Simon kata: angkat tangan kanan!","Simon kata: tepuk tangan 3 kali!","Angkat tangan kiri! (jangan ikuti, ini tanpa Simon kata)","Simon kata: buat lingkaran dengan jari!","Simon kata: jabat tangan temanmu!"]}',
                'desc' => 'Versi tangan dari permainan Simon Kata yang seru.',
                'id' => 74,
                'image' => NULL,
                'moral' => 'Mendengarkan dengan baik adalah kunci keberhasilan.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["mendengarkan","mengelola_marah"]',
                'slug' => 'simon-kata-tangan',
                'sort_order' => 3,
                'status' => 'approved',
                'title' => 'Simon Kata Tangan',
                'type' => 'permainan_tangan',
                'views' => 7,
            ],
            [
                'active' => 1,
                'agama' => NULL,
                'ages' => '[5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"pages":[{"num":1,"text":"Tiko, kucing kecil yang penasaran, menemukan lubang misterius di bawah pohon besar."},{"num":2,"text":"Tiko masuk ke lubang itu dan menemukan terowongan gelap yang berliku-liku."},{"num":3,"text":"Di ujung terowongan, Tiko melihat cahaya terang dan keluar ke dunia ajaib."},{"num":4,"text":"Di dunia ajaib, pohon-pohon bisa bicara dan bunga-bunga bernyanyi."},{"num":5,"text":"Tiko bertemu kelinci bernama Lolo yang sedang mencari wortel ajaib."},{"num":6,"text":"Lolo mengajak Tiko ke gunung pelangi untuk menemukan wortel itu."},{"num":7,"text":"Di perjalanan, mereka melewati sungai susu dan jembatan permen."},{"num":8,"text":"Mereka bertemu burung hantu bijaksana yang memberi petunjuk jalan."},{"num":9,"text":"Tiko dan Lolo sampai di puncak gunung dan menemukan wortel ajaib."},{"num":10,"text":"Wortel ajaib itu ternyata bisa membuat semua hewan di dunia ajaib bahagia."},{"num":11,"text":"Tiko dan Lolo membagikan wortel ajaib kepada semua teman-teman mereka."},{"num":12,"text":"Semua hewan di dunia ajaib merayakan dengan pesta besar yang meriah."},{"num":13,"text":"Tiko belajar bahwa berbagi membuat hati lebih bahagia dari memiliki segalanya."},{"num":14,"text":"Tiko pulang ke dunianya dengan cerita indah dan hati yang penuh sukacita."},{"num":15,"text":"Tiko menceritakan petualangannya kepada Ibu dan adik-adiknya."},{"num":16,"text":"Malam itu Tiko tidur dengan senyuman, bermimpi tentang dunia ajaib."}]}',
                'desc' => 'Tiko, kucing kecil yang penasaran, menemukan dunia ajaib di bawah pohon besar dan belajar tentang berbagi dan persahabatan.',
                'id' => 75,
                'image' => 'cover.png',
                'moral' => 'Berbagi membuat hati lebih bahagia dari memiliki segalanya. Persahabatan sejati tumbuh dari kebaikan hati.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => 'A 16-panel comic page, single image with a 4x4 panel grid.

Title: Tiko di Dunia Ajaib
Description: Tiko, kucing kecil yang penasaran, menemukan dunia ajaib di bawah pohon besar dan belajar tentang berbagi dan persahabatan.
Moral: Berbagi membuat hati lebih bahagia dari memiliki segalanya. Persahabatan sejati tumbuh dari kebaikan hati.

Each panel is a comic panel illustration:

Panel 1 (cover): Title "Tiko di Dunia Ajaib" centered, colorful kid-friendly comic illustration representing the story theme.
Panel 2: Tiko masuk ke lubang itu dan menemukan terowongan gelap yang berliku-liku.
Panel 3: Di ujung terowongan, Tiko melihat cahaya terang dan keluar ke dunia ajaib.
Panel 4: Di dunia ajaib, pohon-pohon bisa bicara dan bunga-bunga bernyanyi.
Panel 5: Tiko bertemu kelinci bernama Lolo yang sedang mencari wortel ajaib.
Panel 6: Lolo mengajak Tiko ke gunung pelangi untuk menemukan wortel itu.
Panel 7: Di perjalanan, mereka melewati sungai susu dan jembatan permen.
Panel 8: Mereka bertemu burung hantu bijaksana yang memberi petunjuk jalan.
Panel 9: Tiko dan Lolo sampai di puncak gunung dan menemukan wortel ajaib.
Panel 10: Wortel ajaib itu ternyata bisa membuat semua hewan di dunia ajaib bahagia.
Panel 11: Tiko dan Lolo membagikan wortel ajaib kepada semua teman-teman mereka.
Panel 12: Semua hewan di dunia ajaib merayakan dengan pesta besar yang meriah.
Panel 13: Tiko belajar bahwa berbagi membuat hati lebih bahagia dari memiliki segalanya.
Panel 14: Tiko pulang ke dunianya dengan cerita indah dan hati yang penuh sukacita.
Panel 15: Tiko menceritakan petualangannya kepada Ibu dan adik-adiknya.
Panel 16: Malam itu Tiko tidur dengan senyuman, bermimpi tentang dunia ajaib.

Style: Modern comic book style, bright colorful, kid friendly, expressive characters.

Rules:
- Panel 1 is the cover with title text centered
- cover title is not too big and not too small
- Panel 2-16 is the comic story
- No written text in other panels except cover
- No speech bubbles allowed
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No page number
- Funny expressions, clear visual storytelling
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel',
                'skills' => '[]',
                'slug' => 'tiko-di-dunia-ajaib',
                'sort_order' => 0,
                'status' => 'approved',
                'title' => 'Tiko di Dunia Ajaib',
                'type' => 'komik',
                'views' => 0,
            ],
        ];
        
        DB::table("activities")->truncate();
        DB::table("activities")->insert($dataTables);
    }
}