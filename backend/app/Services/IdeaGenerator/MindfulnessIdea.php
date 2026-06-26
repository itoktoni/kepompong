<?php

namespace App\Services\IdeaGenerator;

class MindfulnessIdea extends BaseIdea
{
    protected function typeName(): string { return 'mindfulness'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Mindfulness & Refleksi',
            'items' => [
                ['num' => 1, 'name' => 'Pernapasan Balon', 'desc' => 'Anak membayangkan perutnya seperti balon yang mengembang dan mengempis saat bernapas.', 'moral' => 'Menenangkan diri melalui pernapasan'],
                ['num' => 2, 'name' => 'Mendengarkan Suara Alam', 'desc' => 'Anak duduk diam dan mendengarkan berbagai suara di sekitar mereka.', 'moral' => 'Menghargai keindahan alam'],
                ['num' => 3, 'name' => 'Jalan Perlahan', 'desc' => 'Berjalan sangat pelan sambil merasakan setiap langkah di tanah.', 'moral' => 'Kesadaran akan tubuh sendiri'],
                ['num' => 4, 'name' => 'Rasa Syukur', 'desc' => 'Setiap anak menyebutkan 3 hal yang mereka syukuri hari ini.', 'moral' => 'Mengembangkan rasa syukur'],
                ['num' => 5, 'name' => 'Gambar Perasaan', 'desc' => 'Anak menggambar perasaan mereka menggunakan warna dan bentuk.', 'moral' => 'Mengenali dan mengekspresikan emosi'],
                ['num' => 6, 'name' => 'Savasana Anak', 'desc' => 'Berbaring santai sambil mendengarkan musik lembut dan membayangkan tempat indah.', 'moral' => 'Relaksasi dan ketenangan batin'],
                ['num' => 7, 'name' => 'Cermin Gerakan', 'desc' => 'Satu anak membuat gerakan pelan, yang lain mengikuti seperti cermin.', 'moral' => 'Fokus dan sinkronisasi'],
                ['num' => 8, 'name' => 'Makan Sadar', 'desc' => 'Makan buah pelan-pelan sambil merasakan tekstur, rasa, dan aroma.', 'moral' => 'Menghargai makanan dan proses'],
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
Generate EXACTLY {$count} UNIQUE ideas for "mindfulness" content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT mindfulness exercise.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the exercise name only, e.g. "Pernapasan Balon", "Mendengarkan Suara Alam", "Rasa Syukur"
- fakta: a comma-separated list of EXACTLY 10 attractive children's mindfulness title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the exercise (steps, duration, benefits)

CORRECT examples:
- topik: "Pernapasan Balon"
- fakta: "Pernapasan Balon yang Menenangkan, Ayo Bernapas Seperti Balon!, Si Balon yang Mengembang, Petualangan Napas Dalam, Pernapasan Balon Ajaib, Rahasia Pernapasan Sehat, Pernapasan Balon dan Tenang, Si Kecil Belajar Bernapas, Pernapasan Balon Gembira, Bernapas Seperti Balon Terbang"
- moral: "Anak membayangkan perutnya seperti balon saat bernapas. Durasi 3-5 menit. Menenangkan sistem saraf."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Exercise name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's mindfulness titles)",
    "moral": "Factual information about the mindfulness exercise"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
