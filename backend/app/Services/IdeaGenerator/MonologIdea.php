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

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null, int $pages = 9): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas for "monolog" content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT monolog topic.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the monolog topic name only, e.g. "Cerita Liburan", "Pidato Mini", "Review Buku"
- fakta: a comma-separated list of EXACTLY 10 attractive children's monolog title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the monolog type (how to perform, skills trained, duration)

CORRECT examples:
- topik: "Cerita Liburan"
- fakta: "Cerita Liburan yang Seru, Ayo Cerita Liburan!, Liburan Impianku, Cerita Liburan ke Pantai, Si Pandai Bercerita, Petualangan Liburan, Cerita Liburan Menyenangkan, Rahasia Liburan Terbaik, Cerita Liburan dan Teman, Si Jago Cerita Liburan"
- moral: "Anak bercerita tentang pengalaman liburan. Melatih keberanian berbicara di depan umum. Durasi 1-3 menit."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Monolog topic name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's monolog titles)",
    "moral": "Factual information about the monolog type"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
