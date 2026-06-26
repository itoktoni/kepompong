<?php

namespace App\Services\IdeaGenerator;

class PuzzleIdea extends BaseIdea
{
    protected function typeName(): string { return 'puzzle'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Puzzle & Problem Solving',
            'items' => [
                ['num' => 1, 'name' => 'Puzzle Gambar Hewan', 'desc' => 'Menyusun potongan puzzle gambar hewan menjadi gambar utuh.', 'moral' => 'Kesabaran menghasilkan keberhasilan'],
                ['num' => 2, 'name' => 'Teka-Teki Angka', 'desc' => 'Mencari pola angka yang hilang dalam deret sederhana.', 'moral' => 'Berpikir logis dan teliti'],
                ['num' => 3, 'name' => 'Labirin Kertas', 'desc' => 'Menemukan jalan keluar dari labirin yang digambar di kertas.', 'moral' => 'Ketekunan dalam memecahkan masalah'],
                ['num' => 4, 'name' => 'Blok Bangunan Tantangan', 'desc' => 'Membangun struktur sesuai pola yang diberikan dengan balok.', 'moral' => 'Mengikuti instruksi dan kreativitas'],
                ['num' => 5, 'name' => 'Cocokkan Pasangan', 'desc' => 'Mencocokkan gambar hewan dengan habitatnya secara berpasangan.', 'moral' => 'Pengetahuan tentang alam dan hubungan'],
                ['num' => 6, 'name' => 'Susun Cerita Acak', 'desc' => 'Mengurutkan kartu bergambar menjadi cerita yang logis.', 'moral' => 'Berpikir runtut dan sistematis'],
                ['num' => 7, 'name' => 'Tangkap Bentuk', 'desc' => 'Mencari dan mengumpulkan benda dengan bentuk tertentu di sekitar ruangan.', 'moral' => 'Pengenalan bentuk dan observasi'],
                ['num' => 8, 'name' => 'Riddle Indonesia', 'desc' => 'Menjawab teka-teki khas Indonesia dengan petunjuk gambar.', 'moral' => 'Mengenal budaya dan berpikir kreatif'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null, int $pages = 9): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas for "puzzle" content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT puzzle type.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the puzzle name only, e.g. "Puzzle Gambar Hewan", "Teka-Teki Angka", "Labirin Kertas"
- fakta: a comma-separated list of EXACTLY 10 attractive children's puzzle title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the puzzle (how to play, skills trained, difficulty)

CORRECT examples:
- topik: "Puzzle Gambar Hewan"
- fakta: "Puzzle Gambar Hewan yang Seru, Ayo Susun Puzzle!, Puzzle Hewan Ajaib, Si Cerdas Susun Puzzle, Petualangan Puzzle Hewan, Puzzle Gambar yang Menarik, Rahasia Puzzle Hewan, Puzzle Hewan Laut, Si Jago Puzzle, Puzzle Hewan Rimba"
- moral: "Menyusun potongan puzzle gambar hewan menjadi gambar utuh. Melatih kesabaran dan spatial reasoning. Cocok usia 3-8 tahun."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Puzzle name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's puzzle titles)",
    "moral": "Factual information about the puzzle"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
