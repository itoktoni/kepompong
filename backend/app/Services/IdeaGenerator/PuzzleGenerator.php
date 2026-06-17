<?php

namespace App\Services\IdeaGenerator;

class PuzzleGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'puzzle';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Puzzle & Problem Solving',
            'items' => [
                ['num' => 1, 'name' => 'Puzzle Gambar Hewan', 'desc' => 'Menyusun potongan puzzle gambar hewan menjadi gambar utuh.', 'moral' => 'Kesabaran menghasilkan keberhasilan'],
                ['num' => 2, 'name' => 'Teka-Teki Angka', 'desc' => 'Mencari pola angka yang hilang dalam deret sederhana.', 'moral' => 'Berpikir logis dan teliti'],
                ['num' => 3, 'name' => 'Labirin Kertas', 'desc' => 'Menemukan jalan keluar dari labirin yang digambar di kertas.', 'moral' => 'Ketekunan dalam memecahkan masalah'],
                ['num' => 4, 'name' => 'Blok Bangunan Tantangan', 'desc' => 'Membangun struktur sesuai pola yang diberikan dengan balok.', 'moral' => 'Mengikuti instruksi dan kreativitas'],
                ['num' => 5, 'name' => 'Cocokkan Pasangan', 'desc' => 'Mencocokkan gambar hewan dengan habitatnya secara berpasangan.', 'moral' => 'Pengetahuan tentang alam dan hubungan'],
                ['num' => 6, 'name' => 'Susun Cerita Acak', 'desc' => 'Mengurutkan kartu bergambar menjadi cerita yang logis.', 'moral' => 'Berpikir runtut dan sistematis'],
                ['num' => 7, 'name' => 'Tangkap Bentuk', 'desc' => 'Mencari dan mengumpulkan benda dengan bentuk tertentu di sekitar ruangan.', 'moral' => 'Pengenalan bentuk dan observasi'],
                ['num' => 8, 'name' => 'Riddle Indonesia', 'desc' => 'Menjawab teka-teki khas Indonesia dengan petunjuk gambar.', 'moral' => 'Mengenal budaya dan berpikir kreatif'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's puzzle and problem-solving designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} puzzle and problem-solving ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate puzzles and problem-solving activities that challenge children's logic, pattern recognition, and critical thinking.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide puzzle dan problem solving untuk anak', $count, $theme);
    }
}
