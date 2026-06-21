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
        $count = max(1, min(20, $count));

        $systemPrompt = 'You are an educational worksheet idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate {$count} educational worksheet ideas for children, based on theme: {$themeList}

IMPORTANT RULES:
- Worksheets must be APPROPRIATE for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use formats: filling, matching, completing, coloring, writing
- DO NOT use "si" in titles
- DO NOT use character/person names
- Ideas must be WORKSHEETS with clear instructions

CORRECT examples:
- "Mewarnai Huruf | huruf A-Z dengan gambar objek"
- "Cocokkan Hewan | gambar hewan dengan nama hewan"
- "Isi Angka | melengkapi urutan angka"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Worksheet Type | Topic | Short description",
    "fakta": "Worksheet content details (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
