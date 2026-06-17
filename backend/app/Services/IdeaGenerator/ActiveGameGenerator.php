<?php

namespace App\Services\IdeaGenerator;

class ActiveGameGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'permainan_aktif';
    }

    public function generate(): array
    {
        return [
            'title' => 'Permainan Aktif dan Gerak',
            'items' => [
                ['num' => 1, 'name' => 'Tangkap Ekor', 'desc' => 'Setiap anak memiliki "ekor" (kain), harus merebut ekor anak lain tanpa kehilangan miliknya.', 'moral' => 'Strategi dan kelincahan sangat penting'],
                ['num' => 2, 'name' => 'Petak Umpet', 'desc' => 'Satu anak menghitung sementara yang lain bersembunyi, lalu mencarinya satu per satu.', 'moral' => 'Bersembunyi dengan cerdas dan tahu waktu yang tepat untuk muncul'],
                ['num' => 3, 'name' => 'Lompat Tali', 'desc' => 'Melompati tali yang diayunkan oleh dua anak lain, melatih koordinasi dan ritme.', 'moral' => 'Ketekunan menghasilkan kemajuan'],
                ['num' => 4, 'name' => 'Gobak Sodor', 'desc' => 'Dua tim berusaha melewati garis lawan tanpa tertangkap, melatih strategi dan kecepatan.', 'moral' => 'Setiap langkah harus dipikirkan dengan matang'],
                ['num' => 5, 'name' => 'Bola Basket Mini', 'desc' => 'Anak-anak melempar bola ke keranjang rendah dengan jarak yang sudah ditentukan.', 'moral' => 'Latihan terus-menerus menghasilkan perbaikan'],
                ['num' => 6, 'name' => 'Tari Kelompok', 'desc' => 'Anak-anak menari bersama dengan musik dan koordinasi gerakan yang seragam.', 'moral' => 'Bersatu dalam perbedaan menciptakan keindahan'],
                ['num' => 7, 'name' => 'Balap Karung', 'desc' => 'Anak melompat di dalam karung menuju garis finish, latihan keseimbangan dan kekuatan.', 'moral' => 'Tidak boleh menyerah meski terlihat sulit'],
                ['num' => 8, 'name' => 'Permainan Engklek', 'desc' => 'Melompati petak-petak yang digambar di tanah, melatih kelincahan dan konsentrasi.', 'moral' => 'Aturan harus dipatuhi untuk fair play'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's activity and game designer for Indonesian preschool and elementary school children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} active physical game ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate active physical games that get children moving, running, jumping, or dancing. Focus on physical health, coordination, and gross motor skills.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide permainan aktif dan gerak untuk anak', $count);
    }
}
