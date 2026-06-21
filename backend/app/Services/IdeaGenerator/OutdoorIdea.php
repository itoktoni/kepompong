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

        $systemPrompt = 'Kamu adalah generator ide eksplorasi outdoor untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide eksplorasi outdoor untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Aktivitas harus AMAN dan MUDAH dilakukan anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan aktivitas alam: mengamati, mengumpulkan, menanam, berjalan
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA AKTIVITAS OUTDOOR yang bisa dilakukan

Contoh yang BENAR:
- "Berburu Harta Karun Alam > mencari daun, batu, benda alam"
- "Mengamati Awan > menebak bentuk awan"
- "Kebun Mini > menanam benih dalam pot"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Aktivitas > Lokasi > Deskripsi singkat",
    "fakta": "Detail cara melakukan aktivitas (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
