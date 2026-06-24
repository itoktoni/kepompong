<?php

namespace App\Services\IdeaGenerator;

class MengenalBendaIdea extends BaseIdea
{
    protected function typeName(): string { return 'mengenal_benda'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Mengenal Benda',
            'items' => [
                ['num' => 1, 'name' => 'Alat Dapur', 'desc' => 'Anak mengenal nama dan fungsi alat-alat dapur seperti panci, sendok, dan piring.', 'moral' => 'Mengenal benda di sekitar dan fungsinya'],
                ['num' => 2, 'name' => 'Alat Sekolah', 'desc' => 'Anak mengenal nama dan fungsi alat sekolah seperti pensil, buku, dan penghapus.', 'moral' => 'Mengenal alat tulis dan kegunaannya'],
                ['num' => 3, 'name' => 'Kendaraan', 'desc' => 'Anak mengenal berbagai jenis kendaraan seperti mobil, sepeda, dan kereta.', 'moral' => 'Mengenal kendaraan dan fungsinya'],
                ['num' => 4, 'name' => 'Buah-buahan', 'desc' => 'Anak mengenal nama, bentuk, warna, dan rasa buah-buahan.', 'moral' => 'Mengenal buah dan khasiatnya'],
                ['num' => 5, 'name' => 'Hewan Peliharaan', 'desc' => 'Anak mengenal hewan peliharaan seperti kucing, anjing, dan ikan.', 'moral' => 'Mengenal hewan dan cara merawatnya'],
                ['num' => 6, 'name' => 'Pakaian', 'desc' => 'Anak mengenal jenis pakaian seperti baju, celana, dan sepatu.', 'moral' => 'Mengenal pakaian dan fungsinya'],
                ['num' => 7, 'name' => 'Sayuran', 'desc' => 'Anak mengenal nama dan bentuk sayuran seperti wortel, bayam, dan tomat.', 'moral' => 'Mengenal sayuran dan manfaatnya'],
                ['num' => 8, 'name' => 'Alat Musik', 'desc' => 'Anak mengenal alat musik seperti gitar, drum, dan piano.', 'moral' => 'Mengenal alat musik dan suaranya'],
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
Generate EXACTLY {$count} UNIQUE ideas for "mengenal_benda" (knowing objects) content type, based on theme: {$themeList}

Each idea MUST be about a DIFFERENT category of objects/things children can learn about.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the object category name only, e.g. "Alat Dapur", "Kendaraan", "Hewan Peliharaan"
- fakta: a comma-separated list of EXACTLY 10 attractive children's object learning title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the objects (what children will learn, names, functions, characteristics)

CORRECT examples:
- topik: "Alat Dapur"
- fakta: "Mengenal Panci Ajaib, Sendok dan Garpu, Piring Cantik, Wajan Besar, Mangkuk Kecil, Gelas Lucu, Talenan Kayu, Pisau Dapur, Sutil Masak, Tutup Panci"
- moral: "Anak mengenal nama dan fungsi alat-alat dapur. Melatih pengetahuan benda di sekitar rumah."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Object category name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's object learning titles)",
    "moral": "Factual information about the objects"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
