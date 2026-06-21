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
        $count = max(1, min(50, $count));

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages like Chinese such as 它的. DO NOT use difficult/foreign words like: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate {$count} ideas for "storytelling" content type (create story ideas with main character, conflict, and resolution), based on theme: {$themeList}

Ideas must be specific facts/knowledge that can be used as storytelling content.

IMPORTANT RULES:
- DO NOT use "si" in titles (example WRONG: "Raja si Paus", CORRECT: "Paus Sperma di Laut Banda")
- DO NOT use character/person names (example WRONG: "Sari si Paus", CORRECT: "Paus Sperma di Laut Banda")
- Ideas must be GLOBAL, not specific stories with characters
- Format: Animal/Object | Object Location | fact, story, information, legend

CORRECT examples:
- "Paus Sperma | Laut Banda | bisa menyelam hingga 3 kilometer untuk mencari makanan di kedalaman laut"
- "Otak | Laboratorium Di Kepala | mengontrol seluruh tubuh dengan miliaran sel saraf"
- "Pari Manta | Raja Ampat | bisa terbang melompat keluar air, sayapnya bisa mencapai 7 meter"

WRONG examples (DO NOT follow):
- "Raja si Paus Sperma yang Bisa Menyelam" (has "si")
- "Sari si Penyanyi Paus" (has character name)

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Animal/Object | Object Location | fact, story, information, legend",
    "fakta": "detailed description of fact, story, information, legend (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
