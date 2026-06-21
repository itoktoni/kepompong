<?php

namespace App\Services\IdeaGenerator;

class IlmuPengetahuanIdea extends BaseIdea
{
    protected function typeName(): string { return 'ilmu_pengetahuan'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Ilmu Pengetahuan',
            'items' => [
                ['num' => 1, 'name' => 'Vulkanizer Sederhana', 'desc' => 'Membuat gunung berapi mini dari baking soda dan cuka.', 'moral' => 'Reaksi kimia dasar yang menyenangkan'],
                ['num' => 2, 'name' => 'Pelangi dalam Gelas', 'desc' => 'Menyusun air berwarna berbeda berdasarkan densitasnya.', 'moral' => 'Belajar tentang massa jenis'],
                ['num' => 3, 'name' => 'Tanaman Kacang', 'desc' => 'Mengamati pertumbuhan kacang dari biji hingga tumbuh tunas.', 'moral' => 'Kesabaran dan siklus kehidupan'],
                ['num' => 4, 'name' => 'Magnet Ajaib', 'desc' => 'Menguji benda mana yang bisa ditarik magnet.', 'moral' => 'Eksplorasi sifat material'],
                ['num' => 5, 'name' => 'Bayangan Matahari', 'desc' => 'Mengamati pergerakan bayangan sepanjang hari.', 'moral' => 'Memahami rotasi bumi'],
                ['num' => 6, 'name' => 'Kupu-kupu dari Ulat', 'desc' => 'Mengamati metamorfosis kupu-kupu dari ulat hingga sayap.', 'moral' => 'Keajaiban siklus hidup'],
                ['num' => 7, 'name' => 'Teleskop Botol', 'desc' => 'Membuat teleskop sederhana dari botol plastik.', 'moral' => 'Kreativitas dalam sains'],
                ['num' => 8, 'name' => 'Cuaca Harian', 'desc' => 'Mencatat cuaca setiap hari dan membuat grafik sederhana.', 'moral' => 'Observasi dan pencatatan data'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide ilmu pengetahuan dan eksperimen sains untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide ilmu pengetahuan dan eksperimen sains untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Eksperimen harus AMAN dan MUDAH dilakukan anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan bahan yang mudah didapat: air, baking soda, kertas, dll
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA EKSPERIMEN atau FAKTA SAINS

Contoh yang BENAR:
- "Gunung Berapi Mini > dari baking soda dan cuka"
- "Pelangi dalam Gelas > air berwarna berdasarkan densitas"
- "Magnet Ajaib > menguji benda yang bisa ditarik magnet"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Eksperimen/Fakta > Bahan/Objek > Deskripsi singkat",
    "fakta": "Detail cara membuat/melakukan eksperimen (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
