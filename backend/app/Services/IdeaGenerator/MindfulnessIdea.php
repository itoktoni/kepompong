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
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide mindfulness dan refleksi untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide mindfulness dan refleksi untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Latihan harus MUDAH dilakukan anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan aktivitas sederhana: pernapasan, mendengarkan, merasakan, bersyukur
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA LATIHAN MINDFULNESS yang bisa dilakukan

Contoh yang BENAR:
- "Pernapasan Balon > membayangkan perut seperti balon"
- "Mendengarkan Suara Alam > duduk diam mendengarkan suara"
- "Rasa Syukur > menyebutkan hal yang disyukuri"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Latihan > Aktivitas > Deskripsi singkat",
    "fakta": "Detail cara melakukan latihan (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
