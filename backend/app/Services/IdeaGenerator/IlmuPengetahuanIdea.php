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

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a science and science experiment idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE science and experiment ideas for children, based on theme: {$themeList}

Each idea MUST be a DIFFERENT experiment with DIFFERENT materials and DIFFERENT scientific concept.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST be UNIQUE (no duplicates)
- Experiments must be SAFE and EASY for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use easily available materials: water, baking soda, paper, magnets, plants, ice
- Each idea MUST have specific materials and specific steps
- DO NOT use "si" in titles
- DO NOT use character/person names

CORRECT examples:
- "Gunung Berapi Mini | Baking soda dan cuka | reaksi kimia menghasilkan busa seperti letusan gunung berapi"
- "Pelangi dalam Gelas | Air dan pewarna makanan | menyusun air berwarna berdasarkan berat jenisnya"
- "Magnet Ajaib | Magnet dan klip kertas | menguji benda mana yang bisa ditarik magnet di sekitar rumah"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Experiment Name | Specific Materials | Scientific concept",
    "fakta": "Details on how to perform the experiment (3-5 specific sentences with steps)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
