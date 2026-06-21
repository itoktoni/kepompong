<?php

namespace App\Services\IdeaGenerator;

class PuzzleIdea extends BaseIdea
{
    protected function typeName(): string { return 'puzzle'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Puzzle & Problem Solving',
            'items' => [
                ['num' => 1, 'name' => 'Puzzle Gambar Hewan', 'desc' => 'Menyusun potongan puzzle gambar hewan menjadi gambar utuh.', 'moral' => 'Kesabaran menghasilkan keberhasilan'],
                ['num' => 2, 'name' => 'Teka-Teki Angka', 'desc' => 'Mencari pola angka yang hilang dalam deret sederhana.', 'moral' => 'Berpikir logis dan teliti'],
                ['num' => 3, 'name' => 'Labirin Kertas', 'desc' => 'Menemukan jalan keluar dari labirin yang digambar di kertas.', 'moral' => 'Ketekunan dalam memecahkan masalah'],
                ['num' => 4, 'name' => 'Blok Bangunan Tantangan', 'desc' => 'Membangun struktur sesuai pola yang diberikan dengan balok.', 'moral' => 'Mengikuti instruksi dan kreativitas'],
                ['num' => 5, 'name' => 'Cocokkan Pasangan', 'desc' => 'Mencocokkan gambar hewan dengan habitatnya secara berpasangan.', 'moral' => 'Pengetahuan tentang alam dan hubungan'],
                ['num' => 6, 'name' => 'Susun Cerita Acak', 'desc' => 'Mengurutkan kartu bergambar menjadi cerita yang logis.', 'moral' => 'Berpikir runtut dan sistematis'],
                ['num' => 7, 'name' => 'Tangkap Bentuk', 'desc' => 'Mencari dan mengumpulkan benda dengan bentuk tertentu di sekitar ruangan.', 'moral' => 'Pengenalan bentuk dan observasi'],
                ['num' => 8, 'name' => 'Riddle Indonesia', 'desc' => 'Menjawab teka-teki khas Indonesia dengan petunjuk gambar.', 'moral' => 'Mengenal budaya dan berpikir kreatif'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide puzzle dan teka-teki untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $ageMin = $ages[0] ?? 3;
        $ageMax = $ages[1] ?? 8;

        $userPrompt = <<<PROMPT
Buatlah {$count} ide puzzle dan teka-teki untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Puzzle harus MUDAH dipahami anak usia {$ageMin}-{$ageMax} tahun
- Gunakan konsep sederhana: mencocokkan, mengurutkan, mencari pasangan, melengkapi pola
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus GLOBAL, berupa konsep puzzle yang bisa dibuat

Contoh yang BENAR:
- "Mencocokkan Hewan > habitat hewan laut dengan gambar"
- "Melengkapi Pola > pola bentuk geometri sederhana"
- "Mencari Pasangan > gambar hewan dengan bayangannya"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Puzzle > Konsp > Deskripsi singkat",
    "fakta": "Detail cara membuat/memainkan puzzle (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
