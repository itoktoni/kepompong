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

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "permainan_tangan" (Buat ide permainan jari dan tangan untuk anak.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten permainan_tangan.

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
