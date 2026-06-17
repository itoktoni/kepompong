<?php

namespace App\Services\IdeaGenerator;

class PermainanGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'permainan';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Permainan Seru',
            'items' => [
                ['num' => 1, 'name' => 'Tebak Kata dari Gambar', 'desc' => 'Guru menunjukkan gambar, anak menebak kata yang sesuai.', 'moral' => 'Melatih daya ingat dan keberanian menjawab'],
                ['num' => 2, 'name' => 'Simon Says', 'desc' => 'Mengikuti perintah yang dimulai dengan "Simon Says".', 'moral' => 'Melatih konsentrasi dan mendengar dengan baik'],
                ['num' => 3, 'name' => 'Teka-Teki Lucu', 'desc' => 'Anak menjawab teka-teki sederhana yang jawabannya menggelitik logika.', 'moral' => 'Berpikir kritis dan memahami sebab-akibat'],
                ['num' => 4, 'name' => 'Permainan Memori', 'desc' => 'Mencocokkan kartu dengan gambar yang sama secara berpasangan.', 'moral' => 'Melatih daya ingat dan konsentrasi'],
                ['num' => 5, 'name' => 'Estafet Kelereng', 'desc' => 'Memindahkan kelereng dengan sendok dari garis start ke finish.', 'moral' => 'Kesabaran dan koordinasi motorik'],
                ['num' => 6, 'name' => 'Bola Karaoke', 'desc' => 'Melempar bola sambil bernyanyi, yang menangkap melanjutkan lagu.', 'moral' => 'Keberanian dan kerja sama'],
                ['num' => 7, 'name' => 'Puzzle Raksasa', 'desc' => 'Menyusun potongan puzzle besar secara berkelompok.', 'moral' => 'Kerja sama dan berpikir sistematis'],
                ['num' => 8, 'name' => 'Cacing Raksasa', 'desc' => 'Berbaris memegang bahu teman depan, berjalan melewati rintangan.', 'moral' => 'Koordinasi tim dan kekompakan'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's game designer for Indonesian children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} fun game ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate fun games that combine learning with play. Focus on social skills, teamwork, and cognitive development.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide permainan seru untuk anak', $count, $theme);
    }
}
