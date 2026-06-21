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

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a game and play idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE game ideas for children, based on theme: {$themeList}

Each idea MUST be a DIFFERENT game with DIFFERENT rules and DIFFERENT setting.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST be UNIQUE (no duplicates)
- Games must be EASY to play for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use simple rules: word guessing, relay races, tag, bingo, memory match
- Each idea MUST have a specific setting/location
- DO NOT use "si" in titles
- DO NOT use character/person names

CORRECT examples:
- "Tebak Kata dari Gambar | Di Ruang Kelas | guru menunjukkan gambar hewan, anak menebak namanya"
- "Estafet Kelereng | Di Lapangan Sekolah | memindahkan kelereng dengan sendok dari start ke finish"
- "Simon Says | Di Taman Bermain | mengikuti perintah yang dimulai dengan Simon Says"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Game Type | Specific Setting | Rules description",
    "fakta": "Details on how to play (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
