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

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas for "latihan_otak" (brain training) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT brain exercise.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the exercise name only, e.g. "Hitung Cepat", "Ingat Urutan", "Cari Perbedaan"
- fakta: a comma-separated list of EXACTLY 10 attractive children's brain training title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the exercise (how to play, cognitive skill targeted, duration)

CORRECT examples:
- topik: "Hitung Cepat"
- fakta: "Hitung Cepat yang Seru, Ayo Hitung Cepat!, Si Cepat Berhitung, Petualangan Hitung Cepat, Hitung Cepat Ajaib, Rahasia Hitung Cepat, Hitung Cepat dan Menang, Si Jago Hitung, Hitung Cepat Gembira, Hitung Cepat Bikin Pintar"
- moral: "Anak menjawab soal hitungan sederhana secepat mungkin. Melatih kecepatan berpikir dan ketepatan. Durasi 1-2 menit per ronde."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Exercise name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's brain training titles)",
    "moral": "Factual information about the brain training exercise"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
