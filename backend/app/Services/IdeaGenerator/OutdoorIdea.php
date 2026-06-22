<?php

namespace App\Services\IdeaGenerator;

class OutdoorIdea extends BaseIdea
{
    protected function typeName(): string { return 'outdoor'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Outdoor Exploration',
            'items' => [
                ['num' => 1, 'name' => 'Berburu Harta Karun Alam', 'desc' => 'Anak mencari daun, batu, dan benda alam lainnya sesuai daftar.', 'moral' => 'Mengenal keanekaragaman alam'],
                ['num' => 2, 'name' => 'Mengamati Awan', 'desc' => 'Berbaring di rumput dan menebak bentuk awan yang lewat.', 'moral' => 'Imajinasi dan pengamatan alam'],
                ['num' => 3, 'name' => 'Kebun Mini', 'desc' => 'Menanam benih dalam pot kecil dan merawatnya setiap hari.', 'moral' => 'Tanggung jawab dan kesabaran'],
                ['num' => 4, 'name' => 'Jalan Alam', 'desc' => 'Berjalan di alam terbuka sambil mengamati tumbuhan dan hewan.', 'moral' => 'Menghargai lingkungan hidup'],
                ['num' => 5, 'name' => 'Koleksi Daun', 'desc' => 'Mengumpulkan berbagai jenis daun dan mengelompokkannya.', 'moral' => 'Klasifikasi dan pengetahuan botani'],
                ['num' => 6, 'name' => 'Mandi Hujan Aman', 'desc' => 'Bermain air hujan di area aman sambil mengamati genangan.', 'moral' => 'Menikmati alam dengan aman'],
                ['num' => 7, 'name' => 'Piknik Edukasi', 'desc' => 'Makan bersama di alam terbuka sambil belajar tentang lingkungan.', 'moral' => 'Bersyukur atas nikmat alam'],
                ['num' => 8, 'name' => 'Observasi Serangga', 'desc' => 'Mengamati serangga di taman menggunakan kaca pembesar.', 'moral' => 'Rasa ingin tahu tentang makhluk hidup'],
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
Generate EXACTLY {$count} UNIQUE ideas for "outdoor" content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT outdoor activity.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the activity name only, e.g. "Berburu Harta Karun", "Mengamati Awan", "Kebun Mini"
- fakta: a comma-separated list of EXACTLY 10 attractive children's outdoor title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the activity (what to do, equipment needed, skills learned)

CORRECT examples:
- topik: "Berburu Harta Karun"
- fakta: "Berburu Harta Karun Alam, Ayo Cari Harta Karun!, Petualangan Berburu Harta, Si Penjelajah Cilik, Berburu Harta Karun Seru, Rahasia Harta Karun Tersembunyi, Berburu Harta dan Menemukan, Si Pemberani Berburu Harta, Berburu Harta Karun Ajaib, Petualangan Mencari Harta"
- moral: "Anak mencari daun, batu, dan benda alam sesuai daftar. Melatih observasi dan pengenalan alam. Siapkan tas dan daftar item."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Activity name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's outdoor titles)",
    "moral": "Factual information about the outdoor activity"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
