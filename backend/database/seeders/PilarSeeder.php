<?php

namespace Database\Seeders;

use App\Models\Pilar;
use Illuminate\Database\Seeder;

class PilarSeeder extends Seeder
{
    public function run(): void
    {
        $pilars = [
            ['pilar_key' => 'spiritual', 'pilar_emoji' => '🙏', 'pilar_title' => 'Spiritual & Nilai Kehidupan', 'pilar_subtitle' => 'Kompas moral & makna hidup', 'pilar_color' => '#4CAF50', 'pilar_bg' => '#E8F5E9', 'pilar_ages' => [2,3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 1],
            ['pilar_key' => 'karakter', 'pilar_emoji' => '🦁', 'pilar_title' => 'Karakter & Mental', 'pilar_subtitle' => 'Tangguh & bertanggung jawab', 'pilar_color' => '#FF9800', 'pilar_bg' => '#FFF3E0', 'pilar_ages' => [2,3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 2],
            ['pilar_key' => 'kreatifitas', 'pilar_emoji' => '📚', 'pilar_title' => 'Kreatifitas & Inovasi', 'pilar_subtitle' => 'Suka belajar & rasa ingin tahu', 'pilar_color' => '#2196F3', 'pilar_bg' => '#E3F2FD', 'pilar_ages' => [2,3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 3],
            ['pilar_key' => 'disiplin', 'pilar_emoji' => '🧠', 'pilar_title' => 'Disiplin & Kebiasaan Baik', 'pilar_subtitle' => 'Fokus & atur diri sendiri', 'pilar_color' => '#9C27B0', 'pilar_bg' => '#F3E5F5', 'pilar_ages' => [3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 4],
            ['pilar_key' => 'kemandirian', 'pilar_emoji' => '🧹', 'pilar_title' => 'Kemandirian & Life Skills', 'pilar_subtitle' => 'Mandiri mengurus diri', 'pilar_color' => '#3F51B5', 'pilar_bg' => '#E8EAF6', 'pilar_ages' => [3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 5],
            ['pilar_key' => 'sosial', 'pilar_emoji' => '🤝', 'pilar_title' => 'Sosial & Komunikasi', 'pilar_subtitle' => 'Bergaul & bekerja sama', 'pilar_color' => '#8D6E63', 'pilar_bg' => '#EFEBE9', 'pilar_ages' => [2,3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 6],
            ['pilar_key' => 'emosi', 'pilar_emoji' => '❤️', 'pilar_title' => 'Pengelolaan Emosi & Keluarga', 'pilar_subtitle' => 'Dicintai, aman & dekat keluarga', 'pilar_color' => '#F44336', 'pilar_bg' => '#FFEBEE', 'pilar_ages' => [1,2,3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 7],
            ['pilar_key' => 'kesehatan', 'pilar_emoji' => '💪', 'pilar_title' => 'Kesehatan & Olahraga', 'pilar_subtitle' => 'Tubuh sehat & aktif bergerak', 'pilar_color' => '#009688', 'pilar_bg' => '#E0F2F1', 'pilar_ages' => [1,2,3,4,5,6,7,8,9,10,11], 'pilar_sort_order' => 8],
        ];

        foreach ($pilars as $p) {
            Pilar::updateOrCreate(['pilar_key' => $p['pilar_key']], $p);
        }
    }
}
