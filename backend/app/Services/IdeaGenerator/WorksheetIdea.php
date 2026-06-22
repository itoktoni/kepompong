<?php

namespace App\Services\IdeaGenerator;

class WorksheetIdea extends BaseIdea
{
    protected function typeName(): string { return 'worksheet'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Worksheet Anak',
            'items' => [
                ['num' => 1, 'name' => 'Worksheet Mewarnai Huruf', 'desc' => 'Anak menebalkan dan mewarnai huruf A-Z dengan krayon.', 'moral' => 'Pengenalan huruf dan motorik halus'],
                ['num' => 2, 'name' => 'Worksheet Hitung-Menghitung', 'desc' => 'Menghitung jumlah benda dan menulis angka yang sesuai.', 'moral' => 'Dasar berhitung'],
                ['num' => 3, 'name' => 'Worksheet Cocokkan', 'desc' => 'Mencocokkan gambar dengan kata yang sesuai menggunakan garis.', 'moral' => 'Kosakata dan asosiasi'],
                ['num' => 4, 'name' => 'Worksheet Maze', 'desc' => 'Menemukan jalan keluar dari labirin sederhana di kertas.', 'moral' => 'Berpikir logis dan ketelitian'],
                ['num' => 5, 'name' => 'Worksheet Pola', 'desc' => 'Melanjutkan pola gambar atau warna yang sudah dimulai.', 'moral' => 'Pengenalan pola'],
                ['num' => 6, 'name' => 'Worksheet Menulis Kalimat', 'desc' => 'Menulis kalimat sederhana berdasarkan gambar yang diberikan.', 'moral' => 'Menulis dan literasi'],
                ['num' => 7, 'name' => 'Worksheet Silang Kata Mini', 'desc' => 'Menyelesaikan silang kata sederhana dengan gambar petunjuk.', 'moral' => 'Kosakata dan logika'],
                ['num' => 8, 'name' => 'Worksheet Gambar Bebas', 'desc' => 'Menggambar bebas berdasarkan tema yang diberikan guru.', 'moral' => 'Kreativitas dan ekspresi'],
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
Generate EXACTLY {$count} UNIQUE ideas for "worksheet" content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT worksheet type.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the worksheet name only, e.g. "Worksheet Mewarnai Huruf", "Worksheet Hitung-Menghitung", "Worksheet Cocokkan"
- fakta: a comma-separated list of EXACTLY 10 attractive children's worksheet title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the worksheet (format, subject, skills trained)

CORRECT examples:
- topik: "Worksheet Mewarnai Huruf"
- fakta: "Worksheet Mewarnai Huruf yang Seru, Ayo Mewarnai Huruf!, Si Pintar Mewarnai Huruf, Petualangan Huruf Warna-warni, Worksheet Huruf Ajaib, Rahasia Huruf dan Warna, Worksheet Huruf dan Gambar, Si Kreatif Warna Huruf, Worksheet Huruf Gembira, Mewarnai Huruf Sambil Belajar"
- moral: "Anak menebalkan dan mewarnai huruf A-Z dengan krayon. Melatih pengenalan huruf dan motorik halus. Cocok usia 3-6 tahun."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Worksheet name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's worksheet titles)",
    "moral": "Factual information about the worksheet"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
