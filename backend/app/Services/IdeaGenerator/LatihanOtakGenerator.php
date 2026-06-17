<?php

namespace App\Services\IdeaGenerator;

class LatihanOtakGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'latihan_otak';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Latihan Otak',
            'items' => [
                ['num' => 1, 'name' => 'Hitung Cepat', 'desc' => 'Anak menjawab soal hitungan sederhana secepat mungkin.', 'moral' => 'Kecepatan berpikir dan ketepatan'],
                ['num' => 2, 'name' => 'Ingat Urutan', 'desc' => 'Guru menyebut angka/huruf, anak mengulang urutannya.', 'moral' => 'Melatih daya ingat jangka pendek'],
                ['num' => 3, 'name' => 'Cari Perbedaan', 'desc' => 'Menemukan perbedaan antara dua gambar yang mirip.', 'moral' => 'Observasi dan ketelitian'],
                ['num' => 4, 'name' => 'Kata Berantai', 'desc' => 'Menyebut kata yang dimulai huruf terakhir kata sebelumnya.', 'moral' => 'Kosakata dan kecepatan berpikir'],
                ['num' => 5, 'name' => 'Pola Warna', 'desc' => 'Melanjutkan pola warna yang diberikan guru.', 'moral' => 'Pengenalan pola dan logika'],
                ['num' => 6, 'name' => 'Math Bingo', 'desc' => 'Bingo dengan jawaban soal matematika sederhana.', 'moral' => 'Berhitung sambil bersenang-senang'],
                ['num' => 7, 'name' => 'Tebak Suara', 'desc' => 'Menebak sumber suara yang dimainkan dari rekaman.', 'moral' => 'Pendengaran dan asosiasi'],
                ['num' => 8, 'name' => 'Cerita Terbalik', 'desc' => 'Menceritakan kejadian dari akhir ke awal.', 'moral' => 'Berpikir kreatif dan berbeda'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's brain training activity designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} brain training activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate brain training activities that improve memory, concentration, logical thinking, and cognitive skills.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide latihan otak untuk anak', $count, $theme);
    }
}
