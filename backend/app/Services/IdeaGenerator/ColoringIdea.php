<?php

namespace App\Services\IdeaGenerator;

class ColoringIdea extends BaseIdea
{
    protected function typeName(): string { return 'coloring'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Mewarnai',
            'items' => [
                ['num' => 1, 'name' => 'Mewarnai Hewan', 'desc' => 'Anak mewarnai gambar hewan kesukaan dengan krayon atau pensil warna.', 'moral' => 'Mengenal hewan dan warna'],
                ['num' => 2, 'name' => 'Mewarnai Pemandangan', 'desc' => 'Mewarnai gambar pemandangan alam seperti gunung, pantai, atau sawah.', 'moral' => 'Menghargai keindahan alam'],
                ['num' => 3, 'name' => 'Mewarnai Buah dan Sayur', 'desc' => 'Mewarnai gambar buah dan sayuran sambil belajar nama-namanya.', 'moral' => 'Pengenalan makanan sehat'],
                ['num' => 4, 'name' => 'Mandala Sederhana', 'desc' => 'Mewarnai pola mandala sederhana untuk melatih konsentrasi.', 'moral' => 'Kesabaran dan fokus'],
                ['num' => 5, 'name' => 'Color by Number', 'desc' => 'Mewarnai gambar berdasarkan angka yang menentukan warnanya.', 'moral' => 'Berhitung sambil mewarnai'],
                ['num' => 6, 'name' => 'Mewarnai Keluarga', 'desc' => 'Menggambar dan mewarnai anggota keluarga sendiri.', 'moral' => 'Menghargai keluarga'],
                ['num' => 7, 'name' => 'Mewarnai Kebun Binatang', 'desc' => 'Mewarnai gambar kebun binatang lengkap dengan hewan-hewannya.', 'moral' => 'Pengetahuan tentang satwa'],
                ['num' => 8, 'name' => 'Mewarnai Kreasi Sendiri', 'desc' => 'Anak menggambar bebas lalu mewarnai dengan warna pilihan sendiri.', 'moral' => 'Kebebasan berekspresi'],
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
Generate EXACTLY {$count} UNIQUE ideas for "coloring" content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT coloring subject.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the coloring subject name only, e.g. "Mewarnai Hewan", "Mewarnai Pemandangan", "Mewarnai Buah"
- fakta: a comma-separated list of EXACTLY 10 attractive children's coloring page title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the coloring subject (visual elements, colors, skills trained)

CORRECT examples:
- topik: "Mewarnai Hewan"
- fakta: "Mewarnai Hewan yang Lucu, Ayo Mewarnai Hewan!, Si Kecil Mewarnai Hewan, Petualangan Warna Hewan, Mewarnai Hewan Ajaib, Rahasia Warna Hewan, Mewarnai Hewan Laut, Si Kreatif Warna Hewan, Mewarnai Hewan Rimba, Mewarnai Hewan Piaraan"
- moral: "Anak mewarnai gambar hewan kesukaan dengan krayon atau pensil warna. Melatih motorik halus dan pengenalan warna. Cocok usia 2-8 tahun."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Coloring subject name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's coloring page titles)",
    "moral": "Factual information about the coloring subject"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
