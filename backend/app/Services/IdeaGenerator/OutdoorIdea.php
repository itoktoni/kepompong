<?php

namespace App\Services\IdeaGenerator;

class OutdoorIdea extends BaseIdea
{
    protected function typeName(): string { return 'outdoor'; }

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

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "outdoor" (Buat ide eksplorasi outdoor dan petualangan alam untuk anak.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten outdoor.

ATURAN PENTING:
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus GLOBAL, bukan cerita spesifik dengan tokoh
- Format: Hewan/Objek > Tempat > Fakta spesifik

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Hewan/Objek > Tempat > Fakta singkat",
    "fakta": "Detail lengkap fakta (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
