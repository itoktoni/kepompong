<?php

namespace App\Services\IdeaGenerator;

class MonologIdea extends BaseIdea
{
    protected function typeName(): string { return 'monolog'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Monolog Anak',
            'items' => [
                ['num' => 1, 'name' => 'Cerita Liburan', 'desc' => 'Anak bercerita tentang pengalaman liburan terbaiknya di depan kelas.', 'moral' => 'Keberanian berbicara di depan umum'],
                ['num' => 2, 'name' => 'Pidato Mini', 'desc' => 'Anak menyampaikan pendapat tentang topik sederhana dalam 1 menit.', 'moral' => 'Menyampaikan ide dengan jelas'],
                ['num' => 3, 'name' => 'Bercerita dari Benda', 'desc' => 'Anak memilih satu benda dan bercerita tentangnya secara imajinatif.', 'moral' => 'Kreativitas dan improvisasi'],
                ['num' => 4, 'name' => 'Stand Up Comedy Anak', 'desc' => 'Anak menceritakan hal lucu yang pernah dialaminya.', 'moral' => 'Humor dan kepercayaan diri'],
                ['num' => 5, 'name' => 'Presenter Berita', 'desc' => 'Anak membacakan berita singkat tentang kejadian di sekolah.', 'moral' => 'Kemampuan membaca dan presentasi'],
                ['num' => 6, 'name' => 'Review Buku', 'desc' => 'Anak menceritakan buku favoritnya dan merekomendasikannya.', 'moral' => 'Literasi dan berbagi rekomendasi'],
                ['num' => 7, 'name' => 'Ceritakan Gambar', 'desc' => 'Anak menceritakan apa yang terjadi dalam gambar tanpa menulis.', 'moral' => 'Observasi dan narasi'],
                ['num' => 8, 'name' => 'Terima Kasih', 'desc' => 'Anak menyampaikan rasa terima kasih kepada orang spesial dalam hidupnya.', 'moral' => 'Menghargai orang lain'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a monolog and speech idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE monolog ideas for children, based on theme: {$themeList}

Each idea MUST be a DIFFERENT monolog topic with DIFFERENT context.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST be UNIQUE (no duplicates)
- Monologs must be EASY to understand and say for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use simple topics: experience stories, opinions, feelings, daily life
- DO NOT use "si" in titles
- DO NOT use character/person names

CORRECT examples:
- "Cerita Liburan ke Pantai | bercerita tentang pengalaman bermain pasir dan ombak"
- "Pidato Mini tentang Lingkungan | menyampaikan pentingnya menjaga kebersihan sekolah"
- "Bercerita dari Benda Sehari-hari | memilih tas sekolah dan bercerita tentang isinya"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Monolog Type | Specific Topic | Short description",
    "fakta": "Details on how to perform the monolog (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
