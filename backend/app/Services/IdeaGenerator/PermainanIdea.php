<?php

namespace App\Services\IdeaGenerator;

class PermainanIdea extends BaseIdea
{
    protected function typeName(): string { return 'permainan'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Permainan Seru',
            'items' => [
                ['num' => 1, 'name' => 'Tebak Kata dari Gambar', 'desc' => 'Guru menunjukkan gambar, anak menebak kata yang sesuai.', 'moral' => 'Melatih daya ingat dan keberanian menjawab'],
                ['num' => 2, 'name' => 'Simon Says', 'desc' => 'Mengikuti perintah yang dimulai dengan "Simon Says".', 'moral' => 'Melatih konsentrasi dan mendengar dengan baik'],
                ['num' => 3, 'name' => 'Teka-Teki Lucu', 'desc' => 'Anak menjawab teka-teki sederhana yang jawabannya menggelitik logika.', 'moral' => 'Berpikir kritis dan memahami sebab-akibat'],
                ['num' => 4, 'name' => 'Permainan Memori', 'desc' => 'Mencocokkan kartu dengan gambar yang sama secara berpasangan.', 'moral' => 'Melatih daya ingat dan konsentrasi'],
                ['num' => 5, 'name' => 'Estafet Kelereng', 'desc' => 'Memindahkan kelereng dengan sendok dari garis start ke finish.', 'moral' => 'Kesabaran dan koordinasi motorik'],
                ['num' => 6, 'name' => 'Bola Karaoke', 'desc' => 'Melempar bola sambil bernyanyi, yang menangkap melanjutkan lagu.', 'moral' => 'Keberanian dan kerja sama'],
                ['num' => 7, 'name' => 'Puzzle Raksasa', 'desc' => 'Menyusun potongan puzzle besar secara berkelompok.', 'moral' => 'Kerja sama dan berpikir sistematis'],
                ['num' => 8, 'name' => 'Cacing Raksasa', 'desc' => 'Berbaris memegang bahu teman depan, berjalan melewati rintangan.', 'moral' => 'Koordinasi tim dan kekompakan'],
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
Generate EXACTLY {$count} UNIQUE ideas for "permainan" (game) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT game type.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the game name only, e.g. "Tebak Kata", "Estafet Kelereng", "Simon Says"
- fakta: a comma-separated list of EXACTLY 10 attractive children's game title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the game (rules, benefits, skills trained)

CORRECT examples:
- topik: "Tebak Kata"
- fakta: "Tebak Kata yang Seru, Ayo Tebak Kata!, Permainan Kata Ajaib, Tebak Kata Cerdas, Si Jago Tebak Kata, Petualangan Kata, Tebak Kata Bikin Ketagihan, Rahasia Kata Tersembunyi, Tebak Kata dan Menang, Si Cepat Tebak Kata"
- moral: "Permainan melatih daya ingat dan kosakata. Bisa dimainkan 2-10 anak. Cocok untuk usia 4-10 tahun."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Game name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's game titles)",
    "moral": "Factual information about the game"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
