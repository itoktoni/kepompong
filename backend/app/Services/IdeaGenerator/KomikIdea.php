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

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "komik" (Buat ide komik dengan karakter, dialog, dan panel yang menarik.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten komik.

ATURAN PENTING:
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus GLOBAL, bukan cerita spesifik dengan tokoh
- Format: Hewan/Objek > Tempat > Fakta spesifik

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Hewan/Objek > Tempat > Fakta singkat",
    "fakta": "Detail lengkap fakta (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
