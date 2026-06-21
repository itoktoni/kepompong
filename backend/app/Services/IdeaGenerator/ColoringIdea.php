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

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a coloring page idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE coloring page ideas for children, based on theme: {$themeList}

Each idea MUST be a DIFFERENT image/subject with DIFFERENT visual elements and DIFFERENT scene.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST be UNIQUE (no duplicates)
- Images must be EASY to color for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use simple objects: animals, fruits, everyday items, nature, vehicles, toys
- Each idea MUST have specific visual elements described
- DO NOT use "si" in titles
- DO NOT use character/person names

CORRECT examples:
- "Kupu-kupu di Taman Bunga | sayap besar dengan pola lingkaran, dikelilingi 3 bunga matahari"
- "Keranjang Buah-buahan | apel merah, jeruk kuning, anggur ungu, pisang hijau dalam keranjang anyaman"
- "Ikan di Terumbu Karang | ikan badut orange di antara karang dan rumput laut hijau"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Image Subject | Specific Visual Elements | Scene description",
    "fakta": "Image description for coloring (3-5 specific sentences with details)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
