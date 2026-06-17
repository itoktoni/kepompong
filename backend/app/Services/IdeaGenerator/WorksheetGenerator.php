<?php

namespace App\Services\IdeaGenerator;

class WorksheetGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'worksheet';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Worksheet Anak',
            'items' => [
                ['num' => 1, 'name' => 'Worksheet Mewarnai Huruf', 'desc' => 'Anak menebalkan dan mewarnai huruf A-Z dengan krayon.', 'moral' => 'Pengenalan huruf dan motorik halus'],
                ['num' => 2, 'name' => 'Worksheet Hitung-Menghitung', 'desc' => 'Menghitung jumlah benda dan menulis angka yang sesuai.', 'moral' => 'Dasar berhitung'],
                ['num' => 3, 'name' => 'Worksheet Cocokkan', 'desc'              => 'Mencocokkan gambar dengan kata yang sesuai menggunakan garis.', 'moral' => 'Kosakata dan asosiasi'],
                ['num' => 4, 'name' => 'Worksheet Maze', 'desc' => 'Menemukan jalan keluar dari labirin sederhana di kertas.', 'moral' => 'Berpikir logis dan ketelitian'],
                ['num' => 5, 'name' => 'Worksheet Pola', 'desc' => 'Melanjutkan pola gambar atau warna yang sudah dimulai.', 'moral' => 'Pengenalan pola'],
                ['num' => 6, 'name' => 'Worksheet Menulis Kalimat', 'desc' => 'Menulis kalimat sederhana berdasarkan gambar yang diberikan.', 'moral' => 'Menulis dan literasi'],
                ['num' => 7, 'name' => 'Worksheet Silang Kata Mini', 'desc' => 'Menyelesaikan silang kata sederhana dengan gambar petunjuk.', 'moral' => 'Kosakata dan logika'],
                ['num' => 8, 'name' => 'Worksheet Gambar Bebas', 'desc' => 'Menggambar bebas berdasarkan tema yang diberikan guru.', 'moral' => 'Kreativitas dan ekspresi'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's worksheet designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} worksheet activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate worksheet activities that combine learning with fun, focusing on literacy, numeracy, and creative thinking.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide worksheet untuk anak', $count, $theme);
    }
}
