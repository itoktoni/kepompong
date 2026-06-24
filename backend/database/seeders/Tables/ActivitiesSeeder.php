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
                'ages' => '[6,7,8]',
                'created_by' => 1,
                'creator' => 'gemini',
                'data' => '{"pages":[{"num":1,"text":"Di kedalaman laut dalam hidup berbagai ikan. Cahaya dari karang bercahaya menjadi penunjuk jalan utama."},{"num":2,"text":"Adik Ikan Lentera: Semalam cahayanya terang sekali. Sekarang semuanya padam dan suasana jadi gelap gulita. Bibi Ikan Hiu Kerdil: Apa yang terjadi ini?"},{"num":3,"text":"Ayah Gurita: Pasti ada yang sengaja merusaknya agar jalanan jadi berbahaya. Kakak Ikan Pedang: Mungkin ikan baru yang belum tahu aturan."},{"num":4,"text":"Adik Ikan Bintang: Jangan bicara sembarangan! Kita tidak punya bukti apa pun. Ayah Gurita: Lalu siapa lagi kalau bukan dia?"},{"num":5,"text":"Suasana jadi tegang. Semua saling curiga dan tidak berani bergerak ke mana-mana."},{"num":6,"text":"Kakek Ikan Belut: Berhentilah menuduh. Gelap saja sudah cukup menyulitkan, jangan tambah keributan."},{"num":7,"text":"Kakek Ikan Belut: Mari berkumpul. Cari tahu apa yang berubah, bukan siapa yang disalahkan."},{"num":8,"text":"Mereka berenang perlahan mendekati karang. Meraba dan mengamati sekelilingnya dengan hati-hati."},{"num":9,"text":"Adik Ikan Lentera: Lihat! Ada endapan lumpur tebal yang menutupi seluruh permukaan karang itu. Bibi Ikan Hiu Kerdil: Itu sebabnya cahayanya tidak terlihat."},{"num":10,"text":"Kakak Ikan Pedang: Arus air kemarin memang membawa banyak lumpur dari dasar palung. Ayah Gurita: Ternyata bukan ulah siapa-siapa."},{"num":11,"text":"Adik Ikan Bintang: Kalau kita bersihkan lapisan lumpur ini, cahayanya pasti bisa menyala kembali."},{"num":12,"text":"Mereka bekerja sama. Ada yang menggerakkan sirip menyapu, ada yang mendorong gumpalan lumpur menjauh."},{"num":13,"text":"Sedikit demi sedikit lapisan kotoran hilang. Perlahan cahaya lembut mulai memancar kembali dari dalam karang."},{"num":14,"text":"Ayah Gurita: Maaf aku sudah terburu menuduh. Kakak Ikan Pedang: Aku juga salah terlalu cepat menduga."},{"num":15,"text":"Laut dalam kembali terang. Semua belajar bahwa masalah terpecahkan lewat pengamatan bukan lewat dugaan semata."}]}',
                'desc' => 'Lampu alami yang menerangi jalur perjalanan tiba-tiba padam. Semua hewan saling curiga sampai mencari penyebab aslinya.',
                'id' => 776,
                'image' => 'cover.png',
                'moral' => 'Moral cerita ini adalah saat terjadi hal yang tidak biasa, langsung menuduh atau merasa takut tidak akan menyelesaikan masalah. Lebih baik tenang, amati keadaan, kumpulkan petunjuk, dan cari akar permasalahannya bersama. Kebenaran dan solusi akan muncul dengan pikiran yang jernih.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["memecahkan_masalah", "kerja_sama", "mengamati", "menghindari_salah_paham", "berpikir_jernih"]',
                'slug' => 'di-negeri-laut-dalam-cahaya-hilang',
                'sort_order' => 0,
                'status' => 'pending',
                'title' => 'Di Negeri Laut Dalam Cahaya Hilang',
                'type' => 'komik',
                'views' => 0,
            ],
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[7,8,9]',
                'created_by' => 1,
                'creator' => 'gemini',
                'data' => '{"pages":[{"num":1,"text":"Adik Elang Emas punya sayap yang kuat dan penglihatan tajam. Ia dijuluki Pahlawan Hutan."},{"num":2,"text":"Adik Elang: Aku bisa terbang ke mana saja dan melihat apa saja. Bibi Burung Hantu: Kekuatan itu bukan untuk pamer, tapi untuk menjaga hutan ini."},{"num":3,"text":"Suatu hari ia ingin terbang jauh ke pegunungan. Ia lupa menyebarkan pesan peringatan hujan lebat akan datang."},{"num":4,"text":"Hujan turun deras tanpa peringatan. Beberapa sarang terendam dan jalan setapak menjadi licin berbahaya."},{"num":5,"text":"Adik Elang: Maafkan aku! Aku lebih mementingkan keinginan sendiri daripada tugas. Ayah Elang Tua: Kekuatan tanpa tanggung jawab bisa membawa bahaya."},{"num":6,"text":"Ia segera bertindak. Membantu memindahkan sarang ke tempat lebih tinggi dan mengarahkan jalan yang aman."},{"num":7,"text":"Setelah keadaan aman, ia berkumpul dengan semua warga hutan."},{"num":8,"text":"Adik Elang: Mulai sekarang aku akan mengutamakan tugas. Setiap kekuatan yang aku miliki akan kupakai untuk kebaikan bersama."},{"num":9,"text":"Ia membuat jadwal rutin: memantau cuaca, mengawasi batas hutan, dan selalu siap sedia membantu."},{"num":10,"text":"Beberapa minggu kemudian, ia melihat kabut asap mendekat. Ia segera memberi peringatan ke seluruh penjuru."},{"num":11,"text":"Semua hewan bersiap dan menjauh dari jalur asap. Tidak ada yang terluka atau terjebak."},{"num":12,"text":"Bibi Rusa: Berkat kewaspadaanmu, kita semua selamat. Adik Elang: Itu tugas dan tanggung jawabku."},{"num":13,"text":"Ia belajar bahwa menjadi pahlawan bukan soal kekuatan, tapi soal bisa dipercaya."},{"num":14,"text":"Setiap hari ia bekerja dengan sungguh-sungguh. Semua warga hutan merasa aman dan tenang."},{"num":15,"text":"Adik Elang Emas terbang tinggi dengan bangga. Tanggung jawab membuatnya menjadi pahlawan yang sesungguhnya."}]}',
                'desc' => 'Sebagai penjaga hutan, Adik Elang Emas punya kekuatan terbang tinggi. Ia sadar kekuatan harus dipakai untuk melindungi semua warga, bukan hanya untuk dirinya sendiri.',
                'id' => 779,
                'image' => 'cover.png',
                'moral' => 'Moral cerita ini adalah memiliki kekuatan atau kelebihan berarti memiliki tanggung jawab yang lebih besar. Kita harus menjaga apa yang dipercayakan, bertindak dengan hati-hati, dan memastikan keamanan orang lain. Tanggung jawab membuat kita menjadi pribadi yang bisa diandalkan dan dihormati.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["bertanggung_jawab", "dapat_diandalkan", "melindungi", "berani"]',
                'slug' => 'pahlawan-hutan-tanggung-jawab-sang-penjaga',
                'sort_order' => 0,
                'status' => 'pending',
                'title' => 'Pahlawan Hutan: Tanggung Jawab Sang Penjaga',
                'type' => 'komik',
                'views' => 0,
            ],
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[7,8,9,10]',
                'created_by' => 1,
                'creator' => 'gemini',
                'data' => '{"pages":[{"num":1,"text":"Adik Singa Berani terpilih menjadi pemimpin muda di Desa Hewan. Ia punya wibawa dan keberanian."},{"num":2,"text":"Musim kemarau tiba. Aliran sungai utama menyusut drastis. Semua hewan mulai cemas."},{"num":3,"text":"Adik Kuda: Kita harus menggali sumur baru! Adik Gajah: Kita cari sumber air lain di luar desa saja."},{"num":4,"text":"Pendapat berbeda-beda. Tidak ada yang sepakat, pekerjaan tidak kunjung dimulai."},{"num":5,"text":"Adik Singa: Tenanglah. Kita tidak bisa berjalan dengan banyak arah sekaligus. Mari duduk bersama."},{"num":6,"text":"Ia meminta setiap orang menyampaikan pendapatnya. Mendengarkan baik-baik tanpa memotong pembicaraan."},{"num":7,"text":"Adik Singa: Menggali sumur butuh tenaga banyak tapi hasilnya tahan lama. Mencari sumber baru butuh waktu perjalanan jauh."},{"num":8,"text":"Adik Singa: Kita bagi tugas. Sebagian menggali sumur, sebagian lagi mencari petunjuk arah air di sekitar."},{"num":9,"text":"Ia mengatur siapa yang paling kuat untuk menggali, siapa yang paling pandai membaca jalur untuk menjelajah."},{"num":10,"text":"Semua orang merasa dihargai. Mereka bekerja dengan semangat sesuai peran masing-masing."},{"num":11,"text":"Dalam waktu singkat, air ditemukan di dalam tanah dan juga ditemukan aliran air kecil di balik bukit."},{"num":12,"text":"Ayah Kerbau: Berkat kepemimpinanmu, kita bisa menyelesaikan masalah dengan cepat. Adik Singa: Ini hasil kerja kita semua."},{"num":13,"text":"Ia mengajarkan bahwa pemimpin harus bisa menyatukan hati, bukan hanya memberi perintah."},{"num":14,"text":"Desa Hewan menjadi makmur. Setiap ada masalah, semua orang percaya pada cara penyelesaiannya."},{"num":15,"text":"Adik Singa tersenyum. Kepemimpinan yang bijak membawa kebahagiaan bagi seluruh warga."}]}',
                'desc' => 'Saat sungai kering, semua bingung. Adik Singa Berani memimpin dengan bijak, mendengarkan pendapat semua orang dan mengambil keputusan terbaik untuk kepentingan bersama.',
                'id' => 780,
                'image' => 'cover.png',
                'moral' => 'Moral cerita ini adalah kepemimpinan yang baik bukan soal memerintah, tapi soal mengajak, mendengarkan, dan menyatukan kekuatan. Pemimpin yang baik bisa melihat kelebihan setiap orang dan mengarahkannya untuk mencapai tujuan bersama.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["leadership", "bermusyawarah", "menyatukan", "membuat_keputusan"]',
                'slug' => 'pemimpin-cerdas-di-desa-hewan',
                'sort_order' => 0,
                'status' => 'pending',
                'title' => 'Pemimpin Cerdas di Desa Hewan',
                'type' => 'komik',
                'views' => 1,
            ],
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[5,6,7]',
                'created_by' => 1,
                'creator' => 'gemini',
                'data' => '{"pages":[{"num":1,"text":"Di terumbu karang yang indah, hidup banyak jenis hewan laut dengan ciri khas masing-masing."},{"num":2,"text":"Adik Ikan Badut tinggal di sela anemon. Ia suka berenang di tempat yang terang dan tenang."},{"num":3,"text":"Di sebelahnya tinggal Adik Ikan Kelinci Laut yang bergerak lambat dan suka bersembunyi di celah gelap."},{"num":4,"text":"Adik Ikan Badut: Kenapa kamu selalu di tempat gelap saja? Ayo berenang ke tempat yang terang dan asyik."},{"num":5,"text":"Adik Ikan Kelinci Laut: Tubuhku tidak tahan sinar matahari yang terik. Tempat gelap membuatku merasa aman."},{"num":6,"text":"Awalnya Adik Ikan Badut merasa aneh. Ia pikir semua harus suka tempat yang sama dengannya."},{"num":7,"text":"Suatu hari ia melihat Adik Ikan Pari yang berenang dekat dasar, dan Adik Ikan Layang yang suka melesat di arus kuat."},{"num":8,"text":"Adik Ikan Badut bertanya pada Bibi Penyu: Mengapa kita semua punya kebiasaan yang berbeda?"},{"num":9,"text":"Bibi Penyu: Itulah keindahan alam. Perbedaan bukan alasan untuk bertengkar, tapi untuk saling melengkapi."},{"num":10,"text":"Adik Ikan Badut mengerti. Ia tidak lagi memaksa temannya mengikuti keinginannya sendiri."},{"num":11,"text":"Ia mulai berkunjung ke tempat tinggal teman-temannya dengan cara yang tidak mengganggu kenyamanan mereka."},{"num":12,"text":"Adik Ikan Kelinci Laut pun merasa dihargai. Ia juga menceritakan hal-hal menarik yang ada di tempat gelap."},{"num":13,"text":"Mereka saling menjaga. Tidak ada yang merasa terganggu, semua merasa nyaman dengan caranya masing-masing."},{"num":14,"text":"Adik Ikan Badut berkata: Ternyata hidup lebih indah kalau kita menghargai cara hidup orang lain."},{"num":15,"text":"Terumbu karang menjadi tempat yang damai. Toleransi membuat semua makhluk bisa hidup berdampingan dengan bahagia."}]}',
                'desc' => 'Di terumbu karang hidup berbagai ikan dengan kebiasaan berbeda. Adik Ikan Badut belajar menghargai perbedaan agar bisa hidup rukun berdampingan.',
                'id' => 781,
                'image' => 'cover.png',
                'moral' => 'Moral cerita ini adalah setiap makhluk memiliki sifat, kebiasaan, dan kebutuhan yang berbeda. Toleransi berarti menghargai perbedaan itu tanpa memaksakan kehendak sendiri. Hidup berdampingan dengan saling menghormati membuat lingkungan menjadi damai dan aman.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["toleransi", "saling_menghargai", "menghormati_perbedaan", "rukun"]',
                'slug' => 'adik-ikan-badut-dan-tetangganya',
                'sort_order' => 0,
                'status' => 'pending',
                'title' => 'Adik Ikan Badut dan Tetangganya',
                'type' => 'komik',
                'views' => 1,
            ]
        ];
        
        DB::table("activities")->insert($dataTables);
    }
}