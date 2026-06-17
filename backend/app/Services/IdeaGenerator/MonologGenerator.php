<?php

namespace App\Services\IdeaGenerator;

class MonologGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'monolog';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Monolog Anak',
            'items' => [
                ['num' => 1, 'name' => 'Cerita Liburan', 'desc' => 'Anak bercerita tentang pengalaman liburan terbaiknya di depan kelas.', 'moral' => 'Keberanian berbicara di depan umum'],
                ['num' => 2, 'name' => 'Pidato Mini', 'desc' => 'Anak menyampaikan pendapat tentang topik sederhana dalam 1 menit.', 'moral' => 'Menyampaikan ide dengan jelas'],
                ['num' => 3, 'name' => 'Bercerita dari Benda', 'desc' => 'Anak memilih satu benda dan bercerita tentangnya secara imajinatif.', 'moral' => 'Kreativitas dan improvisasi'],
                ['num' => 4, 'name' => 'Stand Up Comedy Anak', 'desc' => 'Anak menceritakan hal lucu yang pernah dialaminya.', 'moral' => 'Humor dan kepercayaan diri'],
                ['num' => 5, 'name' => 'Presenter Berita', 'desc' => 'Anak membacakan berita singkat tentang kejadian di sekolah.', 'moral' => 'Kemampuan membaca dan presentasi'],
                ['num' => 6, 'name' => 'Review Buku', 'desc' => 'Anak menceritakan buku favoritnya dan merekomendasikannya.', 'moral' => 'Literasi dan berbagi rekomendasi'],
                ['num' => 7, 'name' => 'Ceritakan Gambar', 'desc' => 'Anak menceritakan apa yang terjadi dalam gambar tanpa menulis.', 'moral' => 'Observasi dan narasi'],
                ['num' => 8, 'name' => 'Terima Kasih', 'desc' => 'Anak menyampaikan rasa terima kasih kepada orang spesial dalam hidupnya.', 'moral' => 'Menghargai orang lain'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's monologue and public speaking designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} monologue activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate monologue activities that build children's confidence in speaking, storytelling, and self-expression.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide aktivitas monolog untuk anak', $count, $theme);
    }
}
