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
        $count = max(1, min(20, $count));

        $systemPrompt = 'You are an outdoor exploration idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate {$count} outdoor exploration ideas for children, based on theme: {$themeList}

IMPORTANT RULES:
- Activities must be SAFE and EASY for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use nature activities: observing, collecting, planting, walking
- DO NOT use "si" in titles
- DO NOT use character/person names
- Ideas must be OUTDOOR ACTIVITIES that can be done

CORRECT examples:
- "Berburu Harta Karun Alam | mencari daun, batu, benda alam"
- "Mengamati Awan | menebak bentuk awan"
- "Kebun Mini | menanam benih dalam pot"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Activity Type | Location | Short description",
    "fakta": "Details on how to perform the activity (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
