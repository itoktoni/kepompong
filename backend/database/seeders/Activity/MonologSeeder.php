<?php

namespace Database\Seeders\Activity;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MonologSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Hari Pertama di Sekolah',
                'desc' => 'Anak bercerita tentang perasaannya di hari pertama sekolah.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Raka',
                    'emotion' => 'Campuran antara senang dan gugup',
                    'script' => "Hari ini hari pertamaku di sekolah.\nAku sangat gugup, tanganku dingin.\nTapi aku lihat teman-teman baru tersenyum padaku.\nGuru menyapaku dengan ramah.\nTernyata sekolah tidak menakutkan.\nAku senang bisa punya teman baru!",
                    'moral' => 'Anak belajar mengungkapkan perasaan dengan kata-kata.',
                ],
            ],
            [
                'title' => 'Aku Rindu Kakek',
                'desc' => 'Anak bercerita tentang kerinduan pada kakek.',
                'ages' => [5, 6, 7, 8],
                'skills' => ['empati', 'berpikir_kreatif'],
                'data' => [
                    'character' => 'Anak kecil bernama Sari',
                    'emotion' => 'Sedih tapi penuh harapan',
                    'script' => "Kakek tinggal jauh di desa.\nAku rindu cerita-ceritanya sebelum tidur.\nKakek selalu bilang, jangan takut bermimpi besar.\nSuatu hari aku akan naik kereta ke rumah Kakek.\nAku akan peluk Kakek erat-erat.\nSampai saat itu, aku simpan cerita Kakek di hatiku.",
                    'moral' => 'Anak belajar mengungkapkan kerinduan dan kasih sayang.',
                ],
            ],
            [
                'title' => 'Aku dan Sepeda Baru',
                'desc' => 'Anak bercerita tentang kegembiraan mendapat sepeda baru.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Budi',
                    'emotion' => 'Sangat senang dan bangga',
                    'script' => "Wah, Papa memberiku sepeda baru!\nWarnanya merah, sangat bagus!\nAku langsung naik sepeda di depan rumah.\nTernyata belum bisa keseimbangan, aku jatuh.\nTapi aku tidak menangis, aku coba lagi.\nSekarang aku sudah bisa bersepeda sendiri!",
                    'moral' => 'Anak belajar tidak mudah menyerah saat mencoba hal baru.',
                ],
            ],
            [
                'title' => 'Hari Pertama Puasa',
                'desc' => 'Anak bercerita tentang pengalaman puasa pertama.',
                'ages' => [5, 6, 7],
                'skills' => ['empati', 'sabar'],
                'data' => [
                    'character' => 'Anak kecil bernama Aisyah',
                    'emotion' => 'Semangat tapi kadang lapar',
                    'script' => "Hari ini aku ikut puasa seperti Mama dan Papa.\nSahur aku makan nasi dan telur, enak sekali.\nSiang hari perutku lapar dan haus.\nTapi aku ingat, puasa itu menahan lapar.\nAku main boneka supaya tidak ingat makanan.\nMaghrib tiba, aku berbuka dengan senang hati!",
                    'moral' => 'Anak belajar menahan diri dan bersabar.',
                ],
            ],
            [
                'title' => 'Aku Sakit',
                'desc' => 'Anak bercerita tentang pengalaman sakit dan belajar sabar.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['sabar', 'empati'],
                'data' => [
                    'character' => 'Anak kecil bernama Rina',
                    'emotion' => 'Sedih dan tidak enak badan',
                    'script' => "Kepalaku pusing dan badanku panas.\nMama menaruh handuk basah di kepalaku.\nAku tidak bisa bermain di luar hari ini.\nAku sedih, tapi Mama menemani aku.\nMama bilang aku harus minum obat dan istirahat.\nBesok kalau sudah sembuh, aku bisa main lagi.",
                    'moral' => 'Anak belajar sabar saat sakit dan patuh minum obat.',
                ],
            ],
            [
                'title' => 'Aku Ingin Jadi Dokter',
                'desc' => 'Anak bercerita tentang cita-cita menjadi dokter.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Dina',
                    'emotion' => 'Semangat dan penuh harapan',
                    'script' => "Aku punya cita-cita ingin jadi dokter.\nDokter itu hebat, bisa menyembuhkan orang sakit.\nAku suka main dokter-dokteran dengan temanku.\nAku pakai jas putih dan main stetoskop.\nAku bilang, buka mulut ya, sayang aa.\nSuatu hari nanti aku akan jadi dokter sungguhan!",
                    'moral' => 'Anak belajar bermimpi besar dan berusaha.',
                ],
            ],
            [
                'title' => 'Teman Baruku',
                'desc' => 'Anak bercerita tentang bertemu teman baru.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['empati', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Andi',
                    'emotion' => 'Senang dan ramah',
                    'script' => "Hari ini ada anak baru di sekolahku.\nNamanya Rizki, dia duduk di sebelahku.\nAku menyapanya, halo, mau main bareng?\nRizki tersenyum dan mengangguk.\nKami bermain balok bersama di waktu istirahat.\nSekarang Rizki sudah jadi teman baikku!",
                    'moral' => 'Anak belajar menyapa dan berteman dengan orang baru.',
                ],
            ],
            [
                'title' => 'Aku Kehilangan Mainan',
                'desc' => 'Anak bercerita tentang kehilangan mainan favorit.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['sabar', 'berpikir_kreatif'],
                'data' => [
                    'character' => 'Anak kecil bernama Tika',
                    'emotion' => 'Sedih dan kecewa',
                    'script' => "Mobil-mobilanku hilang, aku cari di mana-mana.\nDi bawah tempat tidur, tidak ada.\nDi lemari mainan, juga tidak ada.\nAku mau menangis, tapi Mama bilang jangan sedih.\nMama bantu aku cari bersama-sama.\nTernyata mobilnya ada di dalam kotak warna!",
                    'moral' => 'Anak belajar sabar dan minta bantuan saat butuh.',
                ],
            ],
            [
                'title' => 'Pergi ke Pantai',
                'desc' => 'Anak bercerita tentang liburan ke pantai.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Lala',
                    'emotion' => 'Sangat senang dan kagum',
                    'script' => "Hari ini aku pergi ke pantai bersama keluarga.\nAir lautnya biru dan ombaknya besar.\nAku bermain pasir, membuat istana pasir.\nKaki aku kena air laut, dingin tapi enak.\nAku lihat burung terbang di atas laut.\nPantai itu sangat indah, aku mau ke sini lagi!",
                    'moral' => 'Anak belajar menikmati keindahan alam.',
                ],
            ],
            [
                'title' => 'Aku Dimarahi Mama',
                'desc' => 'Anak bercerita tentang dimarahi dan belajar dari kesalahan.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['sabar', 'empati'],
                'data' => [
                    'character' => 'Anak kecil bernama Doni',
                    'emotion' => 'Sedih dan menyesal',
                    'script' => "Tadi aku memecahkan piring di dapur.\nMama marah karena aku main di dapur.\nAku menangis karena sedih dimarahi.\nTapi aku tahu Mama marah karena sayang aku.\nAku bilang maaf pada Mama.\nMama memelukku dan bilang jangan ulangi ya.",
                    'moral' => 'Anak belajar meminta maaf dan mengakui kesalahan.',
                ],
            ],
            [
                'title' => 'Aku Belajar Renang',
                'desc' => 'Anak bercerita tentang pengalaman belajar renang.',
                'ages' => [5, 6, 7],
                'skills' => ['percaya_diri', 'sabar'],
                'data' => [
                    'character' => 'Anak kecil bernama Sinta',
                    'emotion' => 'Takut tapi mau mencoba',
                    'script' => "Papa mengajakku ke kolam renang.\nAku takut, airnya dalam.\nPapa bilang, jangan takut, Papa di sini.\nAku pegang pelampung dan masuk ke air.\nTernyata airnya dingin tapi enak.\nSekarang aku sudah bisa mengapung sendiri!",
                    'moral' => 'Anak belajar berani mencoba hal baru.',
                ],
            ],
            [
                'title' => 'Hari Raya',
                'desc' => 'Anak bercerita tentang merayakan Hari Raya.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['empati', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Farhan',
                    'emotion' => 'Sangat senang dan bersyukur',
                    'script' => "Hari ini Hari Raya, hari yang ditunggu-tunggu.\nAku pakai baju baru yang bagus.\nAku pergi ke masjid bersama Papa.\nSetelah itu aku minta maaf pada Kakek dan Nenek.\nAku dapat banyak amplop uang.\nHari Raya itu hari yang paling menyenangkan!",
                    'moral' => 'Anak belajar memaafkan dan bersyukur.',
                ],
            ],
            [
                'title' => 'Aku Menanam Benih',
                'desc' => 'Anak bercerita tentang menanam benih dan merawatnya.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['sabar', 'berpikir_kreatif'],
                'data' => [
                    'character' => 'Anak kecil bernama Wati',
                    'emotion' => 'Antusias dan sabar',
                    'script' => "Mama memberiku benih bunga.\nAku tanam benihnya di pot kecil.\nAku siram air setiap pagi dan sore.\nBeberapa hari kemudian muncul tunas kecil.\nAku senang sekali melihat tanamannya tumbuh.\nMerawat tanaman itu butuh sabar dan sayang.",
                    'moral' => 'Anak belajar sabar dan bertanggung jawab.',
                ],
            ],
            [
                'title' => 'Bermain di Hujan',
                'desc' => 'Anak bercerita tentang keseruan bermain hujan.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Yanto',
                    'emotion' => 'Gembira dan bersemangat',
                    'script' => "Hujan turun dengan deras di luar.\nAku pakai sepatu bot dan jas hujan.\nAku loncat-loncat di genangan air.\nAirnya memercik ke mana-mana, seru sekali!\nAku buat perahu dari kertas dan beri di air.\nSetelah itu aku minum teh hangat bersama Mama.",
                    'moral' => 'Anak belajar menikmati alam dengan aman.',
                ],
            ],
            [
                'title' => 'Aku dan Adik Baru',
                'desc' => 'Anak bercerita tentang hadirnya adik baru.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['empati', 'sabar'],
                'data' => [
                    'character' => 'Anak kecil bernama Putri',
                    'emotion' => 'Campuran senang dan cemburu',
                    'script' => "Mama pulang dari rumah sakit membawa adik bayi.\nAdiknya kecil sekali, tangannya mungil.\nAku mau menggendong tapi Mama bilang hati-hati.\nKadang aku sedih karena Mama lebih sibuk dengan adik.\nTapi Mama bilang, tetap sayang Putri sama.\nSekarang aku jadi kakak yang baik untuk adik.",
                    'moral' => 'Anak belajar menerima dan menyayangi adik.',
                ],
            ],
            [
                'title' => 'Aku Tersesat',
                'desc' => 'Anak bercerita tentang pengalaman tersesat.',
                'ages' => [5, 6, 7],
                'skills' => ['percaya_diri', 'berpikir_kreatif'],
                'data' => [
                    'character' => 'Anak kecil bernama Arif',
                    'emotion' => 'Takut tapi berani',
                    'script' => "Aku jalan-jalan di pasar dan hilang dari Mama.\nAku mulai takut, mau menangis.\nTapi aku ingat pesan Mama, minta tolong orang baik.\nAku bilang pada penjaga toko, aku tersesat.\nPenjaga toko menolongku mencari Mama.\nMama memelukku erat-erat, aku lega sekali.",
                    'moral' => 'Anak belajar minta tolong saat dalam bahaya.',
                ],
            ],
            [
                'title' => 'Belajar Mengaji',
                'desc' => 'Anak bercerita tentang pengalaman belajar mengaji.',
                'ages' => [5, 6, 7],
                'skills' => ['konsentrasi', 'sabar'],
                'data' => [
                    'character' => 'Anak kecil bernama Hasan',
                    'emotion' => 'Rajin dan tekun',
                    'script' => "Setiap sore aku pergi ke TPQ belajar mengaji.\nUstaz mengajariku huruf hijaiyah.\nAlif, ba, ta, itu huruf-hurufnya.\nKadang aku lupa, tapi Ustaz sabar mengajariku.\nAku terus berlatih di rumah setiap hari.\nSekarang aku sudah bisa membaca beberapa ayat!",
                    'moral' => 'Anak belajar tekun dan rajin dalam belajar.',
                ],
            ],
            [
                'title' => 'Aku dan Kucingku',
                'desc' => 'Anak bercerita tentang kucing peliharaannya.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['empati', 'berpikir_kreatif'],
                'data' => [
                    'character' => 'Anak kecil bernama Nia',
                    'emotion' => 'Sayang dan bahagia',
                    'script' => "Aku punya kucing bernama Kiki.\nKiki berwarna oranye dan sangat lucu.\nSetiap pagi aku beri Kiki makan.\nKiki suka tidur di pangkuanku.\nKalau aku pulang sekolah, Kiki menyambutku.\nAku sangat sayang pada Kiki!",
                    'moral' => 'Anak belajar menyayangi dan merawat hewan.',
                ],
            ],
            [
                'title' => 'Pergi ke Pasar',
                'desc' => 'Anak bercerita tentang pengalaman pergi ke pasar.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Dewi',
                    'emotion' => 'Antusias dan penasaran',
                    'script' => "Hari ini aku pergi ke pasar bersama Mama.\nPasar itu ramai sekali, banyak orang.\nAku lihat sayuran, buah-buahan, dan ikan.\nMama beli sayur bayam dan buah pepaya.\nAku bantu Mama membawa kantong belanja.\nPasar itu seru, banyak warna dan suara!",
                    'moral' => 'Anak belajar mengenal dunia dan membantu orang tua.',
                ],
            ],
            [
                'title' => 'Aku Ingin Terbang',
                'desc' => 'Anak bercerita tentang imajinasi ingin terbang.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['berpikir_kreatif', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Riko',
                    'emotion' => 'Imajinatif dan penuh harap',
                    'script' => "Aku melihat burung terbang di langit.\nAndai aku bisa terbang seperti burung.\nAku akan terbang ke atas awan yang putih.\nAku akan lihat rumahku dari atas.\nAku akan terbang ke gunung dan laut.\nMungkin suatu hari aku bisa naik pesawat!",
                    'moral' => 'Anak belajar berimajinasi dan bermimpi.',
                ],
            ],
            [
                'title' => 'Hari Ulang Tahun',
                'desc' => 'Anak bercerita tentang merayakan ulang tahun.',
                'ages' => [4, 5, 6, 7],
                'skills' => ['empati', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Vina',
                    'emotion' => 'Sangat senang dan bersyukur',
                    'script' => "Hari ini ulang tahunku yang keenam!\nMama membeli kue tart cokelat yang enak.\nTeman-temanku datang ke rumah.\nKami bernyanyi dan meniup lilin bersama.\nAku dapat banyak hadiah yang lucu.\nTerima kasih Mama Papa, aku sayang kalian!",
                    'moral' => 'Anak belajar bersyukur dan menghargai kasih sayang.',
                ],
            ],
            [
                'title' => 'Aku Membantu Tetangga',
                'desc' => 'Anak bercerita tentang membantu tetangga.',
                'ages' => [5, 6, 7],
                'skills' => ['empati', 'percaya_diri'],
                'data' => [
                    'character' => 'Anak kecil bernama Gilang',
                    'emotion' => 'Baik hati dan senang membantu',
                    'script' => "Nenek tetanggaku sedang sakit.\nAku bantu Nenek membawa belanjaannya.\nNenek tersenyum dan bilang terima kasih.\nAku juga bantu menyiram tanaman Nenek.\nMama bilang aku anak yang baik hati.\nMembantu orang lain itu membuat hati senang!",
                    'moral' => 'Anak belajar menolong sesama dengan ikhlas.',
                ],
            ],
        ];

        $maxOrder = Activity::max('sort_order') ?? 0;

        foreach ($samples as $i => $item) {
            Activity::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => 'monolog',
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
