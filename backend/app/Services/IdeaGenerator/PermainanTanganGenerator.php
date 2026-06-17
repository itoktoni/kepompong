<?php

namespace App\Services\IdeaGenerator;

class PermainanTanganGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'permainan_tangan';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Permainan Tangan',
            'items' => [
                ['num' => 1, 'name' => 'Suit Jepang', 'desc' => 'Anak bermain batu-gunting-kertas dengan aturan tambahan.', 'moral' => 'Keputusan cepat dan sportivitas'],
                ['num' => 2, 'name' => 'Cublak-Cublak Suweng', 'desc' => 'Permainan tradisional mencari benda tersembunyi di tangan.', 'moral' => 'Koordinasi dan tradisi budaya'],
                ['num' => 3, 'name' => 'Tepuk Tangan Berirama', 'desc' => 'Anak berpasangan dan menepuk tangan mengikuti pola ritme.', 'moral' => 'Koordinasi dan sinkronisasi'],
                ['num' => 4, 'name' => 'Boneka Jari', 'desc' => 'Membuat boneka dari kertas dan memainkan cerita mini.', 'moral' => 'Kreativitas dan motorik halus'],
                ['num' => 5, 'name' => 'Congklak Mini', 'desc' => 'Bermain congklak dengan biji-bijian dan lubang di tanah.', 'moral' => 'Strategi dan berhitung'],
                ['num' => 6, 'name' => 'Lompat Karet', 'desc' => 'Melompati karet yang dipegang dua orang dengan ketinggian berbeda.', 'moral' => 'Keseimbangan dan tantangan'],
                ['num' => 7, 'name' => 'Kerincing Jari', 'desc' => 'Membuat alat musik dari tutup botol dan dimainkan di jari.', 'moral' => 'Kreativitas musikal'],
                ['num' => 8, 'name' => 'Puzzle Tangan', 'desc' => 'Membentuk henda atau benda menggunakan jari tangan.', 'moral' => 'Imajinasi dan motorik halus'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's hand game designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} hand game ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate hand games and finger play activities that develop fine motor skills, coordination, and cultural awareness.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide permainan tangan untuk anak', $count, $theme);
    }
}
