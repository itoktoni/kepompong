<?php

namespace App\Services\IdeaGenerator;

class KomikGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'komik';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Komik',
            'items' => [
                ['num' => 1, 'name' => 'Komik Strip 4 Panel', 'desc' => 'Anak menggambar komik strip sederhana dengan 4 panel dan dialog.', 'moral' => 'Bercerita secara visual'],
                ['num' => 2, 'name' => 'Komik Tanpa Kata', 'desc' => 'Membuat komik yang menceritakan hanya menggunakan gambar.', 'moral' => 'Visual storytelling'],
                ['num' => 3, 'name' => 'Komik Foto', 'desc' => 'Mengambil foto dan menambahkan dialog komik menggunakan aplikasi.', 'moral' => 'Kreativitas digital'],
                ['num' => 4, 'name' => 'Komik Edukasi', 'desc' => 'Membuat komik sederhana yang menjelaskan materi pelajaran.', 'moral' => 'Belajar sambil berkarya'],
                ['num' => 5, 'name' => 'Komik Hewan Peliharaan', 'desc' => 'Menggambar petualangan hewan peliharaan dalam format komik.', 'moral' => 'Empati terhadap hewan'],
                ['num' => 6, 'name' => 'Manga Style', 'desc' => 'Belajar menggambar karakter dengan gaya manga sederhana.', 'moral' => 'Mengenal gaya seni berbeda'],
                ['num' => 7, 'name' => 'Komik Kolaborasi', 'desc' => 'Setiap anak menggambar satu panel, disusun menjadi komik bersama.', 'moral' => 'Kerja sama kreatif'],
                ['num' => 8, 'name' => 'Komik Superhero Sekolah', 'desc' => 'Menciptakan superhero dari siswa biasa yang mengatasi masalah sekolah.', 'moral' => 'Imajinasi dan problem solving'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's comic activity designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} comic activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate comic-related activities that develop creativity, visual storytelling, and artistic skills.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide aktivitas komik untuk anak', $count, $theme);
    }
}
