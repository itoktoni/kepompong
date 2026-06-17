<?php

namespace App\Services\IdeaGenerator;

class OutdoorGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'outdoor';
    }

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
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's outdoor exploration activity designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} outdoor activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate outdoor activities that connect children with nature, promote physical activity, and develop environmental awareness.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide aktivitas outdoor untuk anak', $count, $theme);
    }
}
