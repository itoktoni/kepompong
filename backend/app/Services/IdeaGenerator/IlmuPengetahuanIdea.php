<?php

namespace App\Services\IdeaGenerator;

class IlmuPengetahuanIdea extends BaseIdea
{
    protected function typeName(): string { return 'ilmu_pengetahuan'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Ilmu Pengetahuan',
            'items' => [
                ['num' => 1, 'name' => 'Vulkanizer Sederhana', 'desc' => 'Membuat gunung berapi mini dari baking soda dan cuka.', 'moral' => 'Reaksi kimia dasar yang menyenangkan'],
                ['num' => 2, 'name' => 'Pelangi dalam Gelas', 'desc' => 'Menyusun air berwarna berbeda berdasarkan densitasnya.', 'moral' => 'Belajar tentang massa jenis'],
                ['num' => 3, 'name' => 'Tanaman Kacang', 'desc' => 'Mengamati pertumbuhan kacang dari biji hingga tumbuh tunas.', 'moral' => 'Kesabaran dan siklus kehidupan'],
                ['num' => 4, 'name' => 'Magnet Ajaib', 'desc' => 'Menguji benda mana yang bisa ditarik magnet.', 'moral' => 'Eksplorasi sifat material'],
                ['num' => 5, 'name' => 'Bayangan Matahari', 'desc' => 'Mengamati pergerakan bayangan sepanjang hari.', 'moral' => 'Memahami rotasi bumi'],
                ['num' => 6, 'name' => 'Kupu-kupu dari Ulat', 'desc' => 'Mengamati metamorfosis kupu-kupu dari ulat hingga sayap.', 'moral' => 'Keajaiban siklus hidup'],
                ['num' => 7, 'name' => 'Teleskop Botol', 'desc' => 'Membuat teleskop sederhana dari botol plastik.', 'moral' => 'Kreativitas dalam sains'],
                ['num' => 8, 'name' => 'Cuaca Harian', 'desc' => 'Mencatat cuaca setiap hari dan membuat grafik sederhana.', 'moral' => 'Observasi dan pencatatan data'],
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
Generate EXACTLY {$count} UNIQUE ideas for "ilmu_pengetahuan" (science) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT science experiment.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the experiment name only, e.g. "Gunung Berapi Mini", "Pelangi dalam Gelas", "Magnet Ajaib"
- fakta: a comma-separated list of EXACTLY 10 attractive children's science title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the experiment (materials, steps, scientific concept)

CORRECT examples:
- topik: "Gunung Berapi Mini"
- fakta: "Gunung Berapi Mini yang Meletus, Ayo Buat Gunung Berapi!, Eksperimen Gunung Berapi, Si Ilmuwan Cilik, Petualangan Gunung Berapi Mini, Gunung Berapi Ajaib, Rahasia Gunung Berapi Mini, Eksperimen Letusan, Si Peneliti Gunung Berapi, Gunung Berapi dari Baking Soda"
- moral: "Membuat gunung berapi mini dari baking soda dan cuka. Reaksi kimia menghasilkan busa seperti letusan. Bahan mudah didapat di dapur."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Experiment name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's science titles)",
    "moral": "Factual information about the science experiment"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
