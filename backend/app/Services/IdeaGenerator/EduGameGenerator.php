<?php

namespace App\Services\IdeaGenerator;

class EduGameGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'permainan_edukasi';
    }

    public function generate(): array
    {
        return [
            'title' => 'Permainan Edukasi Seru',
            'items' => [
                ['num' => 1, 'name' => 'Tebak Kata dari Gambar', 'desc' => 'Guru menunjukkan gambar, anak menebak kata yang sesuai dengan menyebutkan suku kata atau huruf awal.', 'moral' => 'Melatih daya ingat dan keberanian menjawab'],
                ['num' => 2, 'name' => 'Hitung Benda Sekitar', 'desc' => 'Anak mencari dan menghitung benda-benda di sekeliling dengan jumlah tertentu, misalnya 5 pensil atau 3 buku.', 'moral' => 'Belajar berhitung dengan cara menyenangkan'],
                ['num' => 3, 'name' => 'Susun Huruf Menjadi Kata', 'desc' => 'Huruf-huruf acak disusun menjadi kata yang bermakna, melatih kemampuan membaca dan kosakata.', 'moral' => 'Melatih kemampuan membaca dan kosakata'],
                ['num' => 4, 'name' => 'Warna dan Bentuk', 'desc' => 'Anak mencocokkan benda dengan warna dan bentuk yang sama, seperti balok merah dengan balok merah.', 'moral' => 'Mengenali warna dan bentuk dengan cepat'],
                ['num' => 5, 'name' => 'Bercerita dengan Gambar', 'desc' => 'Anak melihat urutan gambar dan menyusunnya menjadi cerita yang logis dan runtut.', 'moral' => 'Melatih berpikir sistematis dan kreatif'],
                ['num' => 6, 'name' => 'Permainan Simon Says', 'desc' => 'Mengikuti perintah yang dimulai dengan "Simon Says", melatih daya dengar dan ketangkasan.', 'moral' => 'Melatih konsentrasi dan mendengar dengan baik'],
                ['num' => 7, 'name' => 'Teka-Teki Lucu', 'desc' => 'Anak menjawab teka-teki sederhana yang jawabannya menggelitik logika dan membuat tertawa.', 'moral' => 'Berpikir kritis dan memahami hubungan sebab-akibat'],
                ['num' => 8, 'name' => 'Bernyanyi Sambil Belajar', 'desc' => 'Lagu-lagu edukasi yang mengajarkan angka, huruf, atau kosakata baru dengan melodi yang catchy.', 'moral' => 'Belajar melalui musik meningkatkan daya ingat'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's activity and game designer for Indonesian preschool and elementary school children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} educational game ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate educational games that combine learning with fun. Focus on cognitive development, literacy, numeracy, or skill-building activities.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide permainan edukasi untuk anak', $count);
    }
}
