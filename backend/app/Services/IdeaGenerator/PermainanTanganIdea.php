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

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'You are a hand games and finger play idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate {$count} hand game ideas for children, based on theme: {$themeList}

IMPORTANT RULES:
- Games must be EASY to play for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use fingers and hands: clapping, snapping, jumping, holding
- DO NOT use "si" in titles
- DO NOT use character/person names
- Ideas must be HAND GAMES with clear movements

CORRECT examples:
- "Tepuk Tangan Berirama | tepuk tangan mengikuti pola ritme"
- "Suit Jepang | batu-gunting-kertas dengan aturan tambahan"
- "Boneka Jari | membuat boneka dari kertas dan memainkan cerita"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Game Type | Movement | Short description",
    "fakta": "Details on how to play (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
