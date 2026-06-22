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
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words like: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas for "bermain_peran" (role-playing) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT profession/scenario.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the profession/scenario name only, e.g. "Dokter Hewan", "Koki Restoran", "Pilot Pesawat"
- fakta: a comma-separated list of EXACTLY 10 attractive children's role-play title ideas about that profession. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the profession (what they do, tools they use, skills needed)

CORRECT examples:
- topik: "Dokter Hewan"
- fakta: "Dokter Hewan yang Baik Hati, Petualangan Dokter Hewan, Ayo Jadi Dokter Hewan, Dokter Hewan Penyelamat, Si Dokter Hewan yang Ramah, Merawat Hewan dengan Sayang, Dokter Hewan Kecilku, Rahasia Dokter Hewan, Dokter Hewan dan Pasien Lucu, Si Penyembuh Hewan"
- moral: "Dokter hewan merawat hewan yang sakit. Mereka menggunakan stetoskop, termometer, dan suntikan. Butuh belajar 5-6 tahun di universitas."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Profession/scenario name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's role-play titles)",
    "moral": "Factual information about the profession/scenario"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
