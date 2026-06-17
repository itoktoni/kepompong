<?php

namespace App\Services\IdeaGenerator;

class BermainPeranGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'bermain_peran';
    }

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
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's role-play activity designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} role-play activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate role-play activities where children pretend to be different characters or professions. Focus on empathy, social skills, and creativity.\n";
        $systemPrompt .= $this->buildAgeGuide($maxAge) . "\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"items\":[{\"name\":\"...\",\"desc\":\"...\",\"moral\":\"...\"},...]}\n";
        $systemPrompt .= "- Each desc max 100 chars, moral max 60 chars\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge}\n";

        if ($agama) {
            $systemPrompt .= "- Religion: {$agama}\n" . $this->buildAgamaGuide($agama) . "\n";
        }
        if (!empty($skills)) {
            $systemPrompt .= "- Skills to focus on: " . implode(', ', $skills) . "\n";
        }

        $systemPrompt .= "CRITICAL: This content is for CHILDREN. Use ONLY safe, kind, positive language.\n";

        return $this->aiGenerate($systemPrompt, 'Buatkan ide bermain peran untuk anak', $count, $theme);
    }
}
