<?php

namespace App\Services\IdeaGenerator;

class TebakTeakanGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'tebak_teakan';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Tebak-tebakan',
            'items' => [
                ['num' => 1, 'name' => 'Tebak Binatang', 'desc' => 'Guru memberikan clue tentang hewan, anak menebak hewan apa.', 'moral' => 'Pengetahuan tentang hewan'],
                ['num' => 2, 'name' => 'Tebak Buah', 'desc' => 'Anak menebak buah dari deskripsi bentuk, warna, dan rasanya.', 'moral' => 'Pengenalan buah-buahan'],
                ['num' => 3, 'name' => 'Tebak Profesi', 'desc' => 'Menirukan gerakan profesi, teman menebak profesi apa.', 'moral' => 'Mengenal berbagai profesi'],
                ['num' => 4, 'name' => 'Tebak Suara', 'desc' => 'Memutar suara henda/alat, anak menebak sumber suara.', 'moral' => 'Pendengaran dan asosiasi'],
                ['num' => 5, 'name' => 'Tebak Lagu', 'desc' => 'Guru bersenandung lagu, anak menebak judul lagu.', 'moral' => 'Mengenal lagu dan musik'],
                ['num' => 6, 'name' => 'Tebak Emosi', 'desc' => 'Menunjukkan ekspresi wajah, anak menebak emosi apa yang dirasakan.', 'moral' => 'Empati dan pengenalan emosi'],
                ['num' => 7, 'name' => 'Tebak Benda', 'desc' => 'Anak meraba benda dalam kantong tertutup dan menebaknya.', 'moral' => 'Sensorik dan deduksi'],
                ['num' => 8, 'name' => 'Tebak Cerita', 'desc' => 'Guru menceritakan awal cerita, anak menebak kelanjutannya.', 'moral' => 'Imajinasi dan prediksi'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's guessing game designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} guessing game ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate guessing games that develop critical thinking, vocabulary, and observation skills.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide tebak-tebakan untuk anak', $count, $theme);
    }
}
