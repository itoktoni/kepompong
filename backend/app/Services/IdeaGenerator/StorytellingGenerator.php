<?php

namespace App\Services\IdeaGenerator;

class StorytellingGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'storytelling';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Story Telling',
            'items' => [
                ['num' => 1, 'name' => 'Cerita Berantai', 'desc' => 'Setiap anak menambahkan satu kalimat untuk melanjutkan cerita secara bergiliran.', 'moral' => 'Melatih imajinasi dan kemampuan bercerita'],
                ['num' => 2, 'name' => 'Dongeng dengan Boneka', 'desc' => 'Anak menggunakan boneka tangan untuk menceritakan dongeng favorit mereka.', 'moral' => 'Mengekspresikan diri melalui peran'],
                ['num' => 3, 'name' => 'Cerita dari Gambar', 'desc' => 'Anak melihat serangkaian gambar dan menyusunnya menjadi cerita yang kreatif.', 'moral' => 'Melatih berpikir logis dan kreatif'],
                ['num' => 4, 'name' => 'Mendongeng Interaktif', 'desc' => 'Guru bercerita dan anak-anak ikut menirukan suara atau gerakan tokoh.', 'moral' => 'Mendengarkan dengan aktif dan antusias'],
                ['num' => 5, 'name' => 'Cerita Imajinasi', 'desc' => 'Anak menciptakan dunia imajinasi sendiri dan menceritakannya kepada teman.', 'moral' => 'Imajinasi tanpa batas membuka wawasan'],
                ['num' => 6, 'name' => 'Sulih Suara Cerita', 'desc' => 'Anak memberikan suara untuk karakter dalam cerita yang dibacakan guru.', 'moral' => 'Mengasah ekspresi dan intonasi'],
                ['num' => 7, 'name' => 'Teater Mini', 'desc' => 'Anak-anak memerankan adegan singkat dari cerita yang sudah dibacakan.', 'moral' => 'Keberanian tampil di depan teman'],
                ['num' => 8, 'name' => 'Cerita Bergambar', 'desc' => 'Anak menggambar adegan favorit lalu menceritakannya kepada kelompok.', 'moral' => 'Menuangkan ide dalam bentuk visual dan verbal'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(50, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 10;

        $systemPrompt = "You are a children's storytelling activity designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} storytelling activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate storytelling activities that help children develop verbal skills, imagination, and confidence in speaking.\n";
        $systemPrompt .= "Generate title with simple and easy understanding for child contains main character, activity, and background example : Petualangan Marlin si Pelari Cepat di lautan.\n";
        $systemPrompt .= "Generate description with main actor, problem, background, activity for child example : Ikan napoleon dengan benjolan di kepalanya menggunakan kecerdikannya untuk menyelesaikan perselisihan antara karang dan alga di terumbu.\n";
        $systemPrompt .= $this->buildAgeGuide($maxAge) . "\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"items\":[{\"name\":\"...\",\"desc\":\"...\",\"moral\":\"...\"},...]}\n";
        $systemPrompt .= "- Each desc contains characher example marlin, whale, rabit, etc background activity example in forest, jungle, etc max 100 chars, moral max 60 chars\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge}\n";

        if ($agama) {
            $systemPrompt .= "- Religion: {$agama}\n" . $this->buildAgamaGuide($agama) . "\n";
        }
        if (!empty($skills)) {
            $systemPrompt .= "- Skills to focus on: " . implode(', ', $skills) . "\n";
        }

        $systemPrompt .= "CRITICAL: This content is for CHILDREN. Use ONLY safe, kind, positive language.\n";

        return $this->aiGenerate($systemPrompt, 'Buatkan ide aktivitas story telling untuk anak', $count, $theme);
    }
}
