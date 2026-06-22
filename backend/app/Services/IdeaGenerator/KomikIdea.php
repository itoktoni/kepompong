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

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas for "komik" (comic) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT comic story.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the comic story name only, e.g. "Komik Strip 4 Panel", "Komik Tanpa Kata", "Komik Edukasi"
- fakta: a comma-separated list of EXACTLY 10 attractive children's comic title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the comic type (format, tools needed, skills trained)

CORRECT examples:
- topik: "Komik Strip 4 Panel"
- fakta: "Komik Strip 4 Panel yang Lucu, Ayo Buat Komik!, Si Komikus Cilik, Petualangan Komik Strip, Komik Strip Ajaib, Rahasia Komik Strip, Komik Strip dan Dialog, Si Kreatif Bikin Komik, Komik Strip Seru, Komik Strip Bikin Ketawa"
- moral: "Anak menggambar komik strip sederhana dengan 4 panel dan dialog. Bahan: kertas, pensil, spidol warna. Melatih storytelling visual."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Comic story name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's comic titles)",
    "moral": "Factual information about the comic type"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
