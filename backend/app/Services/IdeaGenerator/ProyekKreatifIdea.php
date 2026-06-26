<?php

namespace App\Services\IdeaGenerator;

class ProyekKreatifIdea extends BaseIdea
{
    protected function typeName(): string { return 'proyek_kreatif'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Proyek Kreatif & Seni',
            'items' => [
                ['num' => 1, 'name' => 'Kolase Daun', 'desc' => 'Membuat gambar hewan atau pemandangan dari potongan daun kering.', 'moral' => 'Kreativitas dari bahan alam'],
                ['num' => 2, 'name' => 'Origami Hewan', 'desc' => 'Melipat kertas menjadi bentuk hewan sederhana.', 'moral' => 'Kesabaran dan ketelitian'],
                ['num' => 3, 'name' => 'Lukisan Jari', 'desc' => 'Menggambar menggunakan jari tangan dengan cat air.', 'moral' => 'Ekspresi diri melalui seni'],
                ['num' => 4, 'name' => 'Robot dari Kardus', 'desc' => 'Membuat robot sederhana dari kardus bekas dan tutup botol.', 'moral' => 'Daur ulang dan imajinasi'],
                ['num' => 5, 'name' => 'Mozaik Kertas', 'desc' => 'Menempel potongan kertas warna membentuk gambar indah.', 'moral' => 'Detail dan perencanaan'],
                ['num' => 6, 'name' => 'Cap Kentang', 'desc' => 'Membuat pola stamp dari kentang dan mencetaknya di kertas.', 'moral' => 'Seni cetak sederhana'],
                ['num' => 7, 'name' => 'Kerajinan Tanah Liat', 'desc' => 'Membentuk mangkuk atau hewan dari tanah liat.', 'moral' => 'Motorik halus dan kreativitas'],
                ['num' => 8, 'name' => 'Mural Kelas', 'desc' => 'Bergotong royong menghias dinding kelas dengan gambar tema tertentu.', 'moral' => 'Kerja sama dan kepemilikan bersama'],
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
Generate EXACTLY {$count} UNIQUE ideas for "proyek_kreatif" (creative project) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT creative project.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the project name only, e.g. "Kolase Daun", "Origami Hewan", "Lukisan Jari"
- fakta: a comma-separated list of EXACTLY 10 attractive children's creative project title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the project (materials needed, steps, skills learned)

CORRECT examples:
- topik: "Kolase Daun"
- fakta: "Kolase Daun yang Cantik, Ayo Buat Kolase!, Seni dari Daun Kering, Kolase Daun Kupu-kupu, Si Kreatif dari Alam, Petualangan Daun Kering, Kolase Daun Menakjubkan, Rahasia Kolase Daun, Kolase Daun dan Bunga, Si Seniman Daun Kering"
- moral: "Membuat gambar dari potongan daun kering. Bahan: daun kering, lem, kertas. Melatih kreativitas dan motorik halus."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Project name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's creative project titles)",
    "moral": "Factual information about the creative project"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
