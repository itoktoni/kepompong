<?php

namespace App\Services\IdeaGenerator;

class KomikIdea extends BaseIdea
{
    protected function typeName(): string { return 'komik'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Komik',
            'items' => [
                ['num' => 1, 'name' => 'Komik Strip 4 Panel', 'desc' => 'Anak menggambar komik strip sederhana dengan 4 panel dan dialog.', 'moral' => 'Bercerita secara visual'],
                ['num' => 2, 'name' => 'Komik Tanpa Kata', 'desc' => 'Membuat komik yang menceritakan hanya menggunakan gambar.', 'moral' => 'Visual storytelling'],
                ['num' => 3, 'name' => 'Komik Foto', 'desc' => 'Mengambil foto dan menambahkan dialog komik menggunakan aplikasi.', 'moral' => 'Kreativitas digital'],
                ['num' => 4, 'name' => 'Komik Edukasi', 'desc' => 'Membuat komik sederhana yang menjelaskan materi pelajaran.', 'moral' => 'Belajar sambil berkarya'],
                ['num' => 5, 'name' => 'Komik Hewan Peliharaan', 'desc' => 'Menggambar petualangan hewan peliharaan dalam format komik.', 'moral' => 'Empati terhadap hewan'],
                ['num' => 6, 'name' => 'Manga Style', 'desc' => 'Belajar menggambar karakter dengan gaya manga sederhana.', 'moral' => 'Mengenal gaya seni berbeda'],
                ['num' => 7, 'name' => 'Komik Kolaborasi', 'desc' => 'Setiap anak menggambar satu panel, disusun menjadi komik bersama.', 'moral' => 'Kerja sama kreatif'],
                ['num' => 8, 'name' => 'Komik Superhero Sekolah', 'desc' => 'Menciptakan superhero dari siswa biasa yang mengatasi masalah sekolah.', 'moral' => 'Imajinasi dan problem solving'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide komik untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide komik untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Cerita harus MUDAH dipahami anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan karakter hewan atau anak-anak
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA CERITA KOMIK dengan konflik dan penyelesaian

Contoh yang BENAR:
- "Kucing Penjelajah Laut > kucing berpetualang di dasar laut"
- "Kupu-kupu yang Belajar Terbang > proses belajar terbang"
- "Raja Hutan yang Baik Hati > singa yang suka menolong"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Karakter > Lokasi > Deskripsi cerita",
    "fakta": "Detail cerita komik (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
