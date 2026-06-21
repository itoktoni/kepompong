<?php

namespace App\Services\IdeaGenerator;

class BermainPeranIdea extends BaseIdea
{
    protected function typeName(): string { return 'bermain_peran'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Bermain Peran',
            'items' => [
                ['num' => 1, 'name' => 'Dokter dan Pasien', 'desc' => 'Anak berperan sebagai dokter yang memeriksa pasien dengan alat medis mainan.', 'moral' => 'Belajar empati dan merawat orang lain'],
                ['num' => 2, 'name' => 'Koki Restoran', 'desc' => 'Anak memasak makanan imajinasi dan menyajikannya kepada pelanggan.', 'moral' => 'Kreativitas dalam menyiapkan sesuatu'],
                ['num' => 3, 'name' => 'Pemadam Kebakaran', 'desc' => 'Anak berperan sebagai petugas pemadam yang menyelamatkan boneka dari bahaya.', 'moral' => 'Keberanian dan pertolongan kepada yang membutuhkan'],
                ['num' => 4, 'name' => 'Guru dan Murid', 'desc' => 'Anak bergantian menjadi guru yang mengajarkan pelajaran sederhana.', 'moral' => 'Menjadi panutan dan berbagi ilmu'],
                ['num' => 5, 'name' => 'Toko Kelontong', 'desc' => 'Anak berbelanja dan berjualan menggunakan mainan kasir dan uang mainan.', 'moral' => 'Belajar berhitung dan berinteraksi sosial'],
                ['num' => 6, 'name' => 'Astronot di Luar Angkasa', 'desc' => 'Anak menjelajahi luar angkasa imajinasi dengan pesawat ruang angkasa dari kardus.', 'moral' => 'Mimpi besar dimulai dari imajinasi'],
                ['num' => 7, 'name' => 'Polisi dan Pencuri', 'desc' => 'Anak bermain kejar-kejaran dengan aturan sederhana dan ending yang positif.', 'moral' => 'Keadilan dan keberanian membela yang benar'],
                ['num' => 8, 'name' => 'Pemandu Wisata', 'desc' => 'Anak menjadi pemandu yang menjelaskan tempat-tempat menarik di sekolah.', 'moral' => 'Komunikasi dan pengetahuan tentang lingkungan'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'You are a role-playing idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $ageMin = $ages[0] ?? 3;
        $ageMax = $ages[1] ?? 8;

        $userPrompt = <<<PROMPT
Generate {$count} role-playing ideas for children, based on theme: {$themeList}

IMPORTANT RULES:
- Scenarios must be EASY to play for children aged {$ageMin}-{$ageMax} years old
- Use familiar professions/situations: doctor, chef, teacher, police, astronaut, etc.
- DO NOT use "si" in titles
- DO NOT use character/person names
- Ideas must be GLOBAL, as role-playing scenarios that can be played

CORRECT examples:
- "Dokter Hewan | Merawat hewan sakit di klinik"
- "Koki Sushi | Membuat sushi imajinasi untuk pelanggan"
- "Guru Bahasa Inggris | Mengajarkan kosakata sederhana"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Profession/Situation | Location | Scenario description",
    "fakta": "Role-playing scenario details (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
