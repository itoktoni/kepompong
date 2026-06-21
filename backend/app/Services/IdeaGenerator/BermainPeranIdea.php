<?php

namespace App\Services\IdeaGenerator;

class BermainPeranIdea extends BaseIdea
{
    protected function typeName(): string { return 'bermain_peran'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Bermain Peran',
            'items' => [
                ['num' => 1, 'name' => 'Dokter dan Pasien', 'desc' => 'Anak berperan sebagai dokter yang memeriksa pasien dengan alat medis mainan.', 'moral' => 'Belajar empati dan merawat orang lain'],
                ['num' => 2, 'name' => 'Koki Restoran', 'desc' => 'Anak memasak makanan imajinasi dan menyajikannya kepada pelanggan.', 'moral' => 'Kreativitas dalam menyiapkan sesuatu'],
                ['num' => 3, 'name' => 'Pemadam Kebakaran', 'desc' => 'Anak berperan sebagai petugas pemadam yang menyelamatkan boneka dari bahaya.', 'moral' => 'Keberanian dan pertolongan kepada yang membutuhkan'],
                ['num' => 4, 'name' => 'Guru dan Murid', 'desc' => 'Anak bergantian menjadi guru yang mengajarkan pelajaran sederhana.', 'moral' => 'Menjadi panutan dan berbagi ilmu'],
                ['num' => 5, 'name' => 'Toko Kelontong', 'desc' => 'Anak berbelanja dan berjualan menggunakan mainan kasir dan uang mainan.', 'moral' => 'Belajar berhitung dan berinteraksi sosial'],
                ['num' => 6, 'name' => 'Astronot di Luar Angkasa', 'desc' => 'Anak menjelajahi luar angkasa imajinasi dengan pesawat ruang angkasa dari kardus.', 'moral' => 'Mimpi besar dimulai dari imajinasi'],
                ['num' => 7, 'name' => 'Polisi dan Pencuri', 'desc' => 'Anak bermain kejar-kejaran dengan aturan sederhana dan ending yang positif.', 'moral' => 'Keadilan dan keberanian membela yang benar'],
                ['num' => 8, 'name' => 'Pemandu Wisata', 'desc' => 'Anak menjadi pemandu yang menjelaskan tempat-tempat menarik di sekolah.', 'moral' => 'Komunikasi dan pengetahuan tentang lingkungan'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide bermain peran untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $ageMin = $ages[0] ?? 3;
        $ageMax = $ages[1] ?? 8;

        $userPrompt = <<<PROMPT
Buatlah {$count} ide bermain peran untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Skenario harus MUDAH dimainkan anak usia {$ageMin}-{$ageMax} tahun
- Gunakan profesi/situasi yang familiar: dokter, koki, guru, polisi, astronot, dll
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus GLOBAL, berupa skenario bermain peran yang bisa dimainkan

Contoh yang BENAR:
- "Dokter Hewan > Merawat hewan sakit di klinik"
- "Koki Sushi > Membuat sushi imajinasi untuk pelanggan"
- "Guru Bahasa Inggris > Mengajarkan kosakata sederhana"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Profesi/Situasi > Lokasi > Deskripsi skenario",
    "fakta": "Detail skenario bermain peran (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
