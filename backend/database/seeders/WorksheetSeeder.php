<?php

namespace Database\Seeders;

use App\Models\MasterWorksheet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class WorksheetSeeder extends Seeder
{
    public function run(): void
    {
        $worksheets = [
            ['worksheet_key' => 'mewarnai_alfabet', 'worksheet_icon' => '🖍️', 'worksheet_title' => 'Mewarnai Huruf', 'worksheet_desc' => 'Mewarnai huruf A-Z dengan krayon', 'worksheet_age' => '1-3', 'worksheet_age_label' => '1-3 thn', 'worksheet_ages' => [1,2,3], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_sort_order' => 1],
            ['worksheet_key' => 'mewarnai_angka', 'worksheet_icon' => '🔢', 'worksheet_title' => 'Mewarnai Angka', 'worksheet_desc' => 'Mewarnai angka 1-10', 'worksheet_age' => '1-3', 'worksheet_age_label' => '1-3 thn', 'worksheet_ages' => [1,2,3], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 2],
            ['worksheet_key' => 'tracing_huruf', 'worksheet_icon' => '✏️', 'worksheet_title' => 'Mengikuti Garis Huruf', 'worksheet_desc' => 'Mengikuti garis putus-putus huruf', 'worksheet_age' => '1-3', 'worksheet_age_label' => '1-3 thn', 'worksheet_ages' => [1,2,3], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 3],
            ['worksheet_key' => 'tracing_angka', 'worksheet_icon' => '✏️', 'worksheet_title' => 'Mengikuti Garis Angka', 'worksheet_desc' => 'Mengikuti garis putus-putus angka', 'worksheet_age' => '1-3', 'worksheet_age_label' => '1-3 thn', 'worksheet_ages' => [1,2,3], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_sort_order' => 4],
            ['worksheet_key' => 'garis_zigzag', 'worksheet_icon' => '〰️', 'worksheet_title' => 'Garis Zig Zag', 'worksheet_desc' => 'Mengikuti garis zigzag dan lengkung', 'worksheet_age' => '1-3', 'worksheet_age_label' => '1-3 thn', 'worksheet_ages' => [1,2,3], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FCE4EC', 'worksheet_icon_color' => '#C2185B', 'worksheet_sort_order' => 5],
            ['worksheet_key' => 'cocokan_warna', 'worksheet_icon' => '🎯', 'worksheet_title' => 'Cocokan Warna', 'worksheet_desc' => 'Mencocokkan warna yang sama', 'worksheet_age' => '1-3', 'worksheet_age_label' => '1-3 thn', 'worksheet_ages' => [1,2,3], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E0F2F1', 'worksheet_icon_color' => '#00695C', 'worksheet_sort_order' => 6],

            ['worksheet_key' => 'menulis_huruf', 'worksheet_icon' => '✏️', 'worksheet_title' => 'Menulis Huruf', 'worksheet_desc' => 'Latihan menulis huruf A-Z dengan panduan', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_sort_order' => 7],
            ['worksheet_key' => 'menulis_angka', 'worksheet_icon' => '🔢', 'worksheet_title' => 'Menulis Angka', 'worksheet_desc' => 'Latihan menulis angka 1-10 dengan panduan', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 8],
            ['worksheet_key' => 'menulis_kotak', 'worksheet_icon' => '📝', 'worksheet_title' => 'Menulis Huruf di Kotak', 'worksheet_desc' => 'Menulis huruf A-Z berulang dalam kotak', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_sort_order' => 9],
            ['worksheet_key' => 'menulis_angka_kotak', 'worksheet_icon' => '🔢', 'worksheet_title' => 'Menulis Angka di Kotak', 'worksheet_desc' => 'Menulis angka 1-10 berulang dalam kotak', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 10],
            ['worksheet_key' => 'menebalkan_huruf', 'worksheet_icon' => '🖊️', 'worksheet_title' => 'Menebalkan Huruf', 'worksheet_desc' => 'Menebalkan huruf A-Z dengan mengikuti garis putus-putus', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 11],
            ['worksheet_key' => 'menebalkan_angka', 'worksheet_icon' => '🔢', 'worksheet_title' => 'Menebalkan Angka', 'worksheet_desc' => 'Menebalkan angka 1-10 dengan mengikuti garis putus-putus', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 12],
            ['worksheet_key' => 'huruf_kapital', 'worksheet_icon' => '🔤', 'worksheet_title' => 'Huruf Kapital A-Z', 'worksheet_desc' => 'Latihan menulis huruf kapital dari A sampai Z', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#0D47A1', 'worksheet_sort_order' => 13],
            ['worksheet_key' => 'angka_1_10', 'worksheet_icon' => '🔢', 'worksheet_title' => 'Angka 1-10', 'worksheet_desc' => 'Latihan menulis angka dari 1 sampai 10', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 14],
            ['worksheet_key' => 'mewarnai_buah', 'worksheet_icon' => '🍎', 'worksheet_title' => 'Mewarnai Buah', 'worksheet_desc' => 'Mewarnai gambar buah-buahan', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_sort_order' => 15],
            ['worksheet_key' => 'mewarnai_hewan', 'worksheet_icon' => '🐱', 'worksheet_title' => 'Mewarnai Hewan', 'worksheet_desc' => 'Mewarnai gambar hewan', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 16],
            ['worksheet_key' => 'mengenal_warna', 'worksheet_icon' => '🎨', 'worksheet_title' => 'Mengenal Warna', 'worksheet_desc' => 'Mengenal dan mencocokkan warna dasar', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FCE4EC', 'worksheet_icon_color' => '#C2185B', 'worksheet_sort_order' => 17],
            ['worksheet_key' => 'mengenal_bentuk', 'worksheet_icon' => '🔷', 'worksheet_title' => 'Mengenal Bentuk', 'worksheet_desc' => 'Mengenal bentuk dasar: lingkaran, segitiga, persegi', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E0F2F1', 'worksheet_icon_color' => '#00695C', 'worksheet_sort_order' => 18],

            ['worksheet_key' => 'penjumlahan', 'worksheet_icon' => '➕', 'worksheet_title' => 'Penjumlahan 1-10', 'worksheet_desc' => 'Latihan penjumlahan angka 1-10', 'worksheet_age' => '4-7', 'worksheet_age_label' => '4-7 thn', 'worksheet_ages' => [4,5,6,7], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 19],
            ['worksheet_key' => 'pengurangan', 'worksheet_icon' => '➖', 'worksheet_title' => 'Pengurangan 1-10', 'worksheet_desc' => 'Latihan pengurangan angka 1-10', 'worksheet_age' => '4-7', 'worksheet_age_label' => '4-7 thn', 'worksheet_ages' => [4,5,6,7], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_sort_order' => 20],
            ['worksheet_key' => 'menyalin_kata', 'worksheet_icon' => '📝', 'worksheet_title' => 'Menyalin Kata', 'worksheet_desc' => 'Menyalin kata-kata sederhana', 'worksheet_age' => '4-7', 'worksheet_age_label' => '4-7 thn', 'worksheet_ages' => [4,5,6,7], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_sort_order' => 21],
            ['worksheet_key' => 'pasangan_benda', 'worksheet_icon' => '🔗', 'worksheet_title' => 'Pasangan Benda', 'worksheet_desc' => 'Mencocokkan benda dengan fungsinya', 'worksheet_age' => '4-7', 'worksheet_age_label' => '4-7 thn', 'worksheet_ages' => [4,5,6,7], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 22],
            ['worksheet_key' => 'fill_blanks', 'worksheet_icon' => '✏️', 'worksheet_title' => 'Lengkapi Huruf', 'worksheet_desc' => 'Isi huruf yang hilang dalam kata', 'worksheet_age' => '4-7', 'worksheet_age_label' => '4-7 thn', 'worksheet_ages' => [4,5,6,7], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FCE4EC', 'worksheet_icon_color' => '#C2185B', 'worksheet_sort_order' => 23],
            ['worksheet_key' => 'dot_to_dot', 'worksheet_icon' => '🔗', 'worksheet_title' => 'Hubungkan Titik', 'worksheet_desc' => 'Hubungkan titik berurutan untuk membentuk gambar', 'worksheet_age' => '4-7', 'worksheet_age_label' => '4-7 thn', 'worksheet_ages' => [4,5,6,7], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E0F2F1', 'worksheet_icon_color' => '#00695C', 'worksheet_sort_order' => 24],
            ['worksheet_key' => 'math_icons', 'worksheet_icon' => '⭐', 'worksheet_title' => 'Berhitung dengan Ikon', 'worksheet_desc' => 'Penjumlahan & pengurangan dengan gambar bintang', 'worksheet_age' => '4-7', 'worksheet_age_label' => '4-7 thn', 'worksheet_ages' => [4,5,6,7], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 25],

            ['worksheet_key' => 'perkalian', 'worksheet_icon' => '✖️', 'worksheet_title' => 'Perkalian 1-5', 'worksheet_desc' => 'Latihan perkalian tabel 1-5', 'worksheet_age' => '6-9', 'worksheet_age_label' => '6-9 thn', 'worksheet_ages' => [6,7,8,9], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 26],
            ['worksheet_key' => 'pembagian', 'worksheet_icon' => '➗', 'worksheet_title' => 'Pembagian 1-10', 'worksheet_desc' => 'Latihan pembagian sederhana', 'worksheet_age' => '6-9', 'worksheet_age_label' => '6-9 thn', 'worksheet_ages' => [6,7,8,9], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_sort_order' => 27],
            ['worksheet_key' => 'menyalin_kalimat', 'worksheet_icon' => '📝', 'worksheet_title' => 'Menyalin Kalimat', 'worksheet_desc' => 'Menyalin kalimat pendek dengan benar', 'worksheet_age' => '6-9', 'worksheet_age_label' => '6-9 thn', 'worksheet_ages' => [6,7,8,9], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_is_api' => true, 'worksheet_sort_order' => 28],
            ['worksheet_key' => 'isi_kata', 'worksheet_icon' => '💬', 'worksheet_title' => 'Melengkapi Kata', 'worksheet_desc' => 'Melengkapi kata yang hilang', 'worksheet_age' => '6-9', 'worksheet_age_label' => '6-9 thn', 'worksheet_ages' => [6,7,8,9], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FCE4EC', 'worksheet_icon_color' => '#C2185B', 'worksheet_sort_order' => 29],
            ['worksheet_key' => 'grafik_batang', 'worksheet_icon' => '📊', 'worksheet_title' => 'Grafik Batang', 'worksheet_desc' => 'Membaca dan menjawab pertanyaan dari grafik batang', 'worksheet_age' => '6-9', 'worksheet_age_label' => '6-9 thn', 'worksheet_ages' => [6,7,8,9], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_sort_order' => 30],
            ['worksheet_key' => 'word_search', 'worksheet_icon' => '🔍', 'worksheet_title' => 'Mencari Kata', 'worksheet_desc' => 'Temukan kata tersembunyi dalam kotak huruf', 'worksheet_age' => '6-9', 'worksheet_age_label' => '6-9 thn', 'worksheet_ages' => [6,7,8,9], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_sort_order' => 31],
            ['worksheet_key' => 'maze', 'worksheet_icon' => '🏁', 'worksheet_title' => 'Labirin', 'worksheet_desc' => 'Temukan jalan keluar dari labirin', 'worksheet_age' => '6-9', 'worksheet_age_label' => '6-9 thn', 'worksheet_ages' => [6,7,8,9], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 32],

            ['worksheet_key' => 'cerita_pendek', 'worksheet_icon' => '📖', 'worksheet_title' => 'Menulis Cerita', 'worksheet_desc' => 'Menulis cerita pendek dari gambar', 'worksheet_age' => '7+', 'worksheet_age_label' => '7+ thn', 'worksheet_ages' => [7,8,9,10,11], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 33],
            ['worksheet_key' => 'rangkuman_buku', 'worksheet_icon' => '📚', 'worksheet_title' => 'Rangkuman Buku', 'worksheet_desc' => 'Membuat rangkuman dari buku acak', 'worksheet_age' => '7+', 'worksheet_age_label' => '7+ thn', 'worksheet_ages' => [7,8,9,10,11], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_is_api' => true, 'worksheet_sort_order' => 34],
            ['worksheet_key' => 'soal_cerita', 'worksheet_icon' => '🧮', 'worksheet_title' => 'Soal Cerita Matematika', 'worksheet_desc' => 'Menyelesaikan soal cerita matematika', 'worksheet_age' => '7+', 'worksheet_age_label' => '7+ thn', 'worksheet_ages' => [7,8,9,10,11], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 35],
            ['worksheet_key' => 'benda_sekitar', 'worksheet_icon' => '🏠', 'worksheet_title' => 'Benda di Sekitar', 'worksheet_desc' => 'Mengenal dan menulis nama benda sekitar', 'worksheet_age' => '7+', 'worksheet_age_label' => '7+ thn', 'worksheet_ages' => [7,8,9,10,11], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_sort_order' => 36],
            ['worksheet_key' => 'geografi', 'worksheet_icon' => '🗺️', 'worksheet_title' => 'Geografi Indonesia', 'worksheet_desc' => 'Mencocokkan kota dengan provinsi', 'worksheet_age' => '7+', 'worksheet_age_label' => '7+ thn', 'worksheet_ages' => [7,8,9,10,11], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_sort_order' => 37],

            ['worksheet_key' => 'menulis_huruf_capital_a-e', 'worksheet_icon' => '🔤', 'worksheet_title' => 'Menulis Huruf Capital A-E', 'worksheet_desc' => 'Latihan menulis huruf kapital A sampai E', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E3F2FD', 'worksheet_icon_color' => '#1565C0', 'worksheet_sort_order' => 38],
            ['worksheet_key' => 'menulis_huruf_capital_f-j', 'worksheet_icon' => '🔤', 'worksheet_title' => 'Menulis Huruf Capital F-J', 'worksheet_desc' => 'Latihan menulis huruf kapital F sampai J', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E8F5E9', 'worksheet_icon_color' => '#2E7D32', 'worksheet_sort_order' => 39],
            ['worksheet_key' => 'menulis_huruf_capital_k-o', 'worksheet_icon' => '🔤', 'worksheet_title' => 'Menulis Huruf Capital K-O', 'worksheet_desc' => 'Latihan menulis huruf kapital K sampai O', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FFF3E0', 'worksheet_icon_color' => '#E65100', 'worksheet_sort_order' => 40],
            ['worksheet_key' => 'menulis_huruf_capital_p-t', 'worksheet_icon' => '🔤', 'worksheet_title' => 'Menulis Huruf Capital P-T', 'worksheet_desc' => 'Latihan menulis huruf kapital P sampai T', 'worksheet_age' => '3-5', 'worksheet_age_label' => '3-5 thn', 'worksheet_ages' => [3,4,5], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#F3E5F5', 'worksheet_icon_color' => '#6A1B9A', 'worksheet_sort_order' => 41],
            ['worksheet_key' => 'tentang_saya', 'worksheet_icon' => '👤', 'worksheet_title' => 'Tentang Saya', 'worksheet_desc' => 'Mengisi biodata diri sendiri', 'worksheet_age' => '3-6', 'worksheet_age_label' => '3-6 thn', 'worksheet_ages' => [3,4,5,6], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#FCE4EC', 'worksheet_icon_color' => '#C2185B', 'worksheet_sort_order' => 42],
            ['worksheet_key' => 'menulis_nama_saya', 'worksheet_icon' => '✏️', 'worksheet_title' => 'Menulis Nama Saya', 'worksheet_desc' => 'Latihan menulis nama sendiri', 'worksheet_age' => '3-6', 'worksheet_age_label' => '3-6 thn', 'worksheet_ages' => [3,4,5,6], 'worksheet_skills' => [], 'worksheet_agama' => null, 'worksheet_plans' => null, 'worksheet_bg' => '#E0F2F1', 'worksheet_icon_color' => '#00695C', 'worksheet_sort_order' => 43],
        ];

        foreach ($worksheets as $w) {
            MasterWorksheet::updateOrCreate(['worksheet_key' => $w['worksheet_key']], $w);
        }

        $this->scanWorksheetFiles($worksheets);
    }

    private function scanWorksheetFiles(array $existing): void
    {
        $existingKeys = array_column($existing, 'worksheet_key');
        $extensions = ['pdf', 'png', 'jpg', 'jpeg', 'webp'];
        $folders = [
            storage_path('app/worksheets'),
            storage_path('app/public/worksheets'),
        ];
        $maxOrder = MasterWorksheet::max('worksheet_sort_order') ?? 0;

        foreach ($folders as $folder) {
            if (!File::isDirectory($folder)) continue;

            foreach (File::files($folder) as $file) {
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, $extensions)) continue;
                if ($file->getFilename() === 'sample.pdf') continue;

                $key = strtolower(str_replace(' ', '_', pathinfo($file->getFilename(), PATHINFO_FILENAME)));
                $key = preg_replace('/[^a-z0-9_\-]/', '', $key);

                if (in_array($key, $existingKeys)) continue;
                if (MasterWorksheet::where('worksheet_key', $key)->exists()) continue;

                $title = str_replace('_', ' ', $key);
                $title = preg_replace_callback('/(?:^|\s)([a-z])/', fn($m) => strtoupper($m[0]), $title);
                $title = preg_replace_callback('/-([a-z])/', fn($m) => '-' . strtoupper($m[1]), $title);

                $maxOrder++;
                MasterWorksheet::create([
                    'worksheet_key' => $key,
                    'worksheet_icon' => '📝',
                    'worksheet_title' => $title,
                    'worksheet_desc' => "Worksheet: {$title}",
                    'worksheet_age' => '3-10',
                    'worksheet_age_label' => '3-10 thn',
                    'worksheet_ages' => [3,4,5,6,7,8,9,10],
                    'worksheet_skills' => [],
                    'worksheet_agama' => null,
                    'worksheet_plans' => null,
                    'worksheet_bg' => '#E8F5E9',
                    'worksheet_icon_color' => '#2E7D32',
                    'worksheet_is_api' => false,
                    'worksheet_sort_order' => $maxOrder,
                    'worksheet_active' => true,
                ]);

                $existingKeys[] = $key;
                $this->line("  Scanned: <info>{$key}</info> (from {$file->getFilename()})");
            }
        }
    }
}
