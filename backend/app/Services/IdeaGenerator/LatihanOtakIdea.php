<?php

namespace App\Services\IdeaGenerator;

class LatihanOtakIdea extends BaseIdea
{
    protected function typeName(): string { return 'latihan_otak'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Latihan Otak',
            'items' => [
                ['num' => 1, 'name' => 'Hitung Cepat', 'desc' => 'Anak menjawab soal hitungan sederhana secepat mungkin.', 'moral' => 'Kecepatan berpikir dan ketepatan'],
                ['num' => 2, 'name' => 'Ingat Urutan', 'desc' => 'Guru menyebut angka/huruf, anak mengulang urutannya.', 'moral' => 'Melatih daya ingat jangka pendek'],
                ['num' => 3, 'name' => 'Cari Perbedaan', 'desc' => 'Menemukan perbedaan antara dua gambar yang mirip.', 'moral' => 'Observasi dan ketelitian'],
                ['num' => 4, 'name' => 'Kata Berantai', 'desc' => 'Menyebut kata yang dimulai huruf terakhir kata sebelumnya.', 'moral' => 'Kosakata dan kecepatan berpikir'],
                ['num' => 5, 'name' => 'Pola Warna', 'desc' => 'Melanjutkan pola warna yang diberikan guru.', 'moral' => 'Pengenalan pola dan logika'],
                ['num' => 6, 'name' => 'Math Bingo', 'desc' => 'Bingo dengan jawaban soal matematika sederhana.', 'moral' => 'Berhitung sambil bersenang-senang'],
                ['num' => 7, 'name' => 'Tebak Suara', 'desc' => 'Menebak sumber suara yang dimainkan dari rekaman.', 'moral' => 'Pendengaran dan asosiasi'],
                ['num' => 8, 'name' => 'Cerita Terbalik', 'desc' => 'Menceritakan kejadian dari akhir ke awal.', 'moral' => 'Berpikir kreatif dan berbeda'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a brain training and brain exercise idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE brain training ideas for children, based on theme: {$themeList}

Each idea MUST be a DIFFERENT exercise with DIFFERENT cognitive skill and DIFFERENT activity.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST be UNIQUE (no duplicates)
- Exercises must be EASY for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use simple concepts: counting, remembering, finding differences, sequencing, matching
- Each idea MUST have a specific cognitive skill targeted
- DO NOT use "si" in titles
- DO NOT use character/person names

CORRECT examples:
- "Hitung Cepat Buah | Di Meja Belajar | menjawab soal hitungan menggunakan gambar buah apel dan jeruk"
- "Ingat Urutan Warna | Di Ruang Bermain | guru menunjukkan 5 kartu warna, anak mengulang urutannya"
- "Cari Perbedaan Gambar | Di Buku Aktivitas | menemukan 5 perbedaan antara dua gambar hewan yang mirip"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Exercise Type | Specific Context | Cognitive skill description",
    "fakta": "Details on how to perform the exercise (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
