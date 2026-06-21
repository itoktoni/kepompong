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

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a mindfulness and reflection idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE mindfulness and reflection ideas for children, based on theme: {$themeList}

Each idea MUST be a DIFFERENT exercise with DIFFERENT activity and DIFFERENT setting.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST be UNIQUE (no duplicates)
- Exercises must be EASY for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use simple activities: breathing, listening, feeling, being grateful, stretching
- Each idea MUST have a specific setting/context
- DO NOT use "si" in titles
- DO NOT use character/person names

CORRECT examples:
- "Pernapasan Balon | Di Ruang Kelas | membayangkan perut seperti balon yang mengembang dan mengempis"
- "Mendengarkan Suara Alam | Di Taman Sekolah | duduk diam dan mendengarkan suara burung dan angin"
- "Rasa Syukur Pagi | Di Kamar Tidur | menyebutkan 3 hal yang disyukuri setelah bangun tidur"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Exercise Type | Specific Setting | Activity description",
    "fakta": "Details on how to perform the exercise (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
