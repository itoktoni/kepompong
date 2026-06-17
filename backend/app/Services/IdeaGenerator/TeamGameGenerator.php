<?php

namespace App\Services\IdeaGenerator;

class TeamGameGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'permainan_kerjasama';
    }

    public function generate(): array
    {
        return [
            'title' => 'Permainan Kerja Sama Tim',
            'items' => [
                ['num' => 1, 'name' => 'Membangun Tower dari Balok', 'desc' => 'Tim berusaha membangun tower tertinggi dari balok dalam waktu tertentu tanpa menjatuhkan.', 'moral' => 'Kerja sama tim menghasilkan sesuatu yang lebih baik'],
                ['num' => 2, 'name' => 'Lari Estafet Berantai', 'desc' => 'Tim berlari membawa tongkat dan meneruskan ke rekan satu tim dengan cepat dan tepat.', 'moral' => 'Setiap peran penting dalam keberhasilan tim'],
                ['num' => 3, 'name' => 'Permainan Kelereng dalam Gelas', 'desc' => 'Tim bekerja sama memindahkan kelereng dari satu wadah ke wadah lain hanya dengan sedotan.', 'moral' => 'Kesabaran dan koordinasi tim sangat penting'],
                ['num' => 4, 'name' => 'Mencari Harta Karun', 'desc' => 'Tim mencari petunjuk yang tersebar di area bermain dan bekerja sama memecahkan teka-teki.', 'moral' => 'Berpikir bersama lebih kuat dari sendiri'],
                ['num' => 5, 'name' => 'Gambar Bersama dengan Mata Tertutup', 'desc' => 'Satu anak menggambar sementara yang lain memberikan instruksi lisan tanpa melihat hasil.', 'moral' => 'Komunikasi yang jelas penting untuk keberhasilan'],
                ['num' => 6, 'name' => 'Permainan Piramida Kertas', 'desc' => 'Tim membuat piramida dari kertas yang dilipat bersama-sama dengan presisi tinggi.', 'moral' => 'Detail kecil sangat berpengaruh pada hasil akhir'],
                ['num' => 7, 'name' => 'Balon dan Sedotan', 'desc' => 'Tim menjaga balon tetap di udara dengan bekerja sama menggunakan bagian tubuh yang berbeda.', 'moral' => 'Semua anggota tim memiliki kontribusi yang berarti'],
                ['num' => 8, 'name' => 'Orchestra Alat Musik Sederhana', 'desc' => 'Setiap anak memainkan alat musik sederhana dan harus terkoordinasi untuk memainkan lagu bersama.', 'moral' => 'Keselarasan dan saling mendengarkan menghasilkan harmoni'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's activity and game designer for Indonesian preschool and elementary school children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} cooperative team game ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate cooperative and team-building games that require children to work together, communicate, and support each other.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide permainan kerja sama tim untuk anak', $count);
    }
}
