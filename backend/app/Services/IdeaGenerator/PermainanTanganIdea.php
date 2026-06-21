<?php

namespace App\Services\IdeaGenerator;

class PermainanTanganIdea extends BaseIdea
{
    protected function typeName(): string { return 'permainan_tangan'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Permainan Tangan',
            'items' => [
                ['num' => 1, 'name' => 'Suit Jepang', 'desc' => 'Anak bermain batu-gunting-kertas dengan aturan tambahan.', 'moral' => 'Keputusan cepat dan sportivitas'],
                ['num' => 2, 'name' => 'Cublak-Cublak Suweng', 'desc' => 'Permainan tradisional mencari benda tersembunyi di tangan.', 'moral' => 'Koordinasi dan tradisi budaya'],
                ['num' => 3, 'name' => 'Tepuk Tangan Berirama', 'desc' => 'Anak berpasangan dan menepuk tangan mengikuti pola ritme.', 'moral' => 'Koordinasi dan sinkronisasi'],
                ['num' => 4, 'name' => 'Boneka Jari', 'desc' => 'Membuat boneka dari kertas dan memainkan cerita mini.', 'moral' => 'Kreativitas dan motorik halus'],
                ['num' => 5, 'name' => 'Congklak Mini', 'desc' => 'Bermain congklak dengan biji-bijian dan lubang di tanah.', 'moral' => 'Strategi dan berhitung'],
                ['num' => 6, 'name' => 'Lompat Karet', 'desc' => 'Melompati karet yang dipegang dua orang dengan ketinggian berbeda.', 'moral' => 'Keseimbangan dan tantangan'],
                ['num' => 7, 'name' => 'Kerincing Jari', 'desc' => 'Membuat alat musik dari tutup botol dan dimainkan di jari.', 'moral' => 'Kreativitas musikal'],
                ['num' => 8, 'name' => 'Puzzle Tangan', 'desc' => 'Membentuk henda atau benda menggunakan jari tangan.', 'moral' => 'Imajinasi dan motorik halus'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide permainan tangan untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide permainan tangan untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Permainan harus MUDAH dimainkan anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan jari dan tangan: tepuk, jentik, lompat, pegang
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA PERMAINAN TANGAN dengan gerakan jelas

Contoh yang BENAR:
- "Tepuk Tangan Berirama > tepuk tangan mengikuti pola ritme"
- "Suit Jepang > batu-gunting-kertas dengan aturan tambahan"
- "Boneka Jari > membuat boneka dari kertas dan memainkan cerita"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Permainan > Gerakan > Deskripsi singkat",
    "fakta": "Detail cara bermain (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
