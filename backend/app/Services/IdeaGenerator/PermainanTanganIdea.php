<?php

namespace App\Services\IdeaGenerator;

class PermainanTanganIdea extends BaseIdea
{
    protected function typeName(): string { return 'permainan_tangan'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Permainan Tangan',
            'items' => [
                ['num' => 1, 'name' => 'Suit Jepang', 'desc' => 'Anak bermain batu-gunting-kertas dengan aturan tambahan.', 'moral' => 'Keputusan cepat dan sportivitas'],
                ['num' => 2, 'name' => 'Cublak-Cublak Suweng', 'desc' => 'Permainan tradisional mencari benda tersembunyi di tangan.', 'moral' => 'Koordinasi dan tradisi budaya'],
                ['num' => 3, 'name' => 'Tepuk Tangan Berirama', 'desc' => 'Anak berpasangan dan menepuk tangan mengikuti pola ritme.', 'moral' => 'Koordinasi dan sinkronisasi'],
                ['num' => 4, 'name' => 'Boneka Jari', 'desc' => 'Membuat boneka dari kertas dan memainkan cerita mini.', 'moral' => 'Kreativitas dan motorik halus'],
                ['num' => 5, 'name' => 'Congklak Mini', 'desc' => 'Bermain congklak dengan biji-bijian dan lubang di tanah.', 'moral' => 'Strategi dan berhitung'],
                ['num' => 6, 'name' => 'Lompat Karet', 'desc' => 'Melompati karet yang dipegang dua orang dengan ketinggian berbeda.', 'moral' => 'Keseimbangan dan tantangan'],
                ['num' => 7, 'name' => 'Kerincing Jari', 'desc' => 'Membuat alat musik dari tutup botol dan dimainkan di jari.', 'moral' => 'Kreativitas musikal'],
                ['num' => 8, 'name' => 'Puzzle Tangan', 'desc' => 'Membentuk henda atau benda menggunakan jari tangan.', 'moral' => 'Imajinasi dan motorik halus'],
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
Generate EXACTLY {$count} UNIQUE ideas for "permainan_tangan" (hand game) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT hand game.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the game name only, e.g. "Suit Jepang", "Tepuk Tangan Berirama", "Boneka Jari"
- fakta: a comma-separated list of EXACTLY 10 attractive children's hand game title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the game (hand movements, rules, skills trained)

CORRECT examples:
- topik: "Tepuk Tangan Berirama"
- fakta: "Tepuk Tangan Berirama yang Seru, Ayo Tepuk Tangan!, Si Ritme Tepuk Tangan, Petualangan Tepuk Berirama, Tepuk Tangan Ajaib, Rahasia Tepuk Tangan, Tepuk Tangan dan Nyanyi, Si Lincah Tepuk Tangan, Tepuk Tangan Gembira, Tepuk Tangan Cepat"
- moral: "Anak berpasangan dan menepuk tangan mengikuti pola ritme. Melatih koordinasi dan sinkronisasi. Bisa sambil bernyanyi."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Game name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's hand game titles)",
    "moral": "Factual information about the hand game"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
