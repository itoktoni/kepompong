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
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a comic idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE comic ideas for children, based on theme: {$themeList}

Each idea MUST be a DIFFERENT story with DIFFERENT characters and DIFFERENT setting.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST be UNIQUE (no duplicates)
- Story must be EASY to understand for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use animal characters or children as main characters
- Each idea MUST have a specific conflict and resolution
- Each idea MUST have a specific location/setting
- DO NOT use "si" in titles
- DO NOT use character/person names

CORRECT examples:
- "Kucing Penjelajah Laut | Di Laut Jawa | kucing berpetualang di dasar laut bertemu ikan badut"
- "Kupu-kupu yang Belajar Terbang | Di Kebun Raya Bogor | proses belajar terbang dari kepompong"
- "Raja Hutan yang Baik Hati | Di Hutan Kalimantan | singa yang suka menolong hewan lain"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Character | Specific Location | Story description",
    "fakta": "Comic story details with conflict and resolution (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
