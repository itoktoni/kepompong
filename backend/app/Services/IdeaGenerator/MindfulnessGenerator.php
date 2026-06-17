<?php

namespace App\Services\IdeaGenerator;

class MindfulnessGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'mindfulness';
    }

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
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's mindfulness and reflection activity designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} mindfulness activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate mindfulness activities that help children calm down, reflect, and develop emotional awareness.\n";
        $systemPrompt .= $this->buildAgeGuide($maxAge) . "\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"items\":[{\"name\":\"...\",\"desc\":\"...\",\"moral\":\"...\"},...]}\n";
        $systemPrompt .= "- Each desc max 100 chars, moral max 60 chars\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge}\n";

        if ($agama) {
            $systemPrompt .= "- Religion: {$agama}\n" . $this->buildAgamaGuide($agama) . "\n";
        }
        if (!empty($skills)) {
            $systemPrompt .= "- Skills to focus on: " . implode(', ', $skills) . "\n";
        }

        $systemPrompt .= "CRITICAL: This content is for CHILDREN. Use ONLY safe, kind, positive language.\n";

        return $this->aiGenerate($systemPrompt, 'Buatkan ide mindfulness dan refleksi untuk anak', $count, $theme);
    }
}
