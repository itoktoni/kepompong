<?php

namespace App\Services\IdeaGenerator;

class StorytellingIdea extends BaseIdea
{
    protected function typeName(): string { return 'storytelling'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Story Telling',
            'items' => [
                ['num' => 1, 'name' => 'Cerita Berantai', 'desc' => 'Setiap anak menambahkan satu kalimat untuk melanjutkan cerita secara bergiliran.', 'moral' => 'Melatih imajinasi dan kemampuan bercerita'],
                ['num' => 2, 'name' => 'Dongeng dengan Boneka', 'desc' => 'Anak menggunakan boneka tangan untuk menceritakan dongeng favorit mereka.', 'moral' => 'Mengekspresikan diri melalui peran'],
                ['num' => 3, 'name' => 'Cerita dari Gambar', 'desc' => 'Anak melihat serangkaian gambar dan menyusunnya menjadi cerita yang kreatif.', 'moral' => 'Melatih berpikir logis dan kreatif'],
                ['num' => 4, 'name' => 'Mendongeng Interaktif', 'desc' => 'Guru bercerita dan anak-anak ikut menirukan suara atau gerakan tokoh.', 'moral' => 'Mendengarkan dengan aktif dan antusias'],
                ['num' => 5, 'name' => 'Cerita Imajinasi', 'desc' => 'Anak menciptakan dunia imajinasi sendiri dan menceritakannya kepada teman.', 'moral' => 'Imajinasi tanpa batas membuka wawasan'],
                ['num' => 6, 'name' => 'Sulih Suara Cerita', 'desc' => 'Anak memberikan suara untuk karakter dalam cerita yang dibacakan guru.', 'moral' => 'Mengasah ekspresi dan intonasi'],
                ['num' => 7, 'name' => 'Teater Mini', 'desc' => 'Anak-anak memerankan adegan singkat dari cerita yang sudah dibacakan.', 'moral' => 'Keberanian tampil di depan teman'],
                ['num' => 8, 'name' => 'Cerita Bergambar', 'desc' => 'Anak menggambar adegan favorit lalu menceritakannya kepada kelompok.', 'moral' => 'Menuangkan ide dalam bentuk visual dan verbal'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages like Chinese such as 它的. DO NOT use difficult/foreign words like: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas for "storytelling" content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT animal/object with DIFFERENT facts.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- Each item MUST have SPECIFIC factual details (size, weight, speed, habitat, behavior, diet, etc.)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- Format: Animal/Object | specific fact with numbers/details

CORRECT examples:
- "Paus Biru | hewan terbesar di dunia, panjangnya bisa mencapai 30 meter dan beratnya 200 ton"
- "Ikan Pari Manta | ikan terbesar di dunia, sayapnya bisa mencapai 7 meter dan bisa hidup sampai 50 tahun"
- "Komodo | kadal terbesar di dunia, panjangnya bisa 3 meter dan bisa berlari sampai 20 km per jam"
- "Orangutan | primata terbesar di Asia, bisa hidup sampai 60 tahun dan 97% DNA-nya sama dengan manusia"

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Animal/Object | specific fact with numbers",
    "fakta": "detailed description with specific facts: size, weight, speed, habitat, behavior, unique abilities (3-5 sentences with real data)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
