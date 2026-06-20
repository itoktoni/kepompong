<?php

namespace App\Services\IdeaGenerator;

class WorksheetGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string { return 'worksheet'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Worksheet Anak',
            'items' => [
                ['num' => 1, 'name' => 'Worksheet Mewarnai Huruf', 'desc' => 'Anak menebalkan dan mewarnai huruf A-Z dengan krayon.', 'moral' => 'Pengenalan huruf dan motorik halus'],
                ['num' => 2, 'name' => 'Worksheet Hitung-Menghitung', 'desc' => 'Menghitung jumlah benda dan menulis angka yang sesuai.', 'moral' => 'Dasar berhitung'],
                ['num' => 3, 'name' => 'Worksheet Cocokkan', 'desc' => 'Mencocokkan gambar dengan kata yang sesuai menggunakan garis.', 'moral' => 'Kosakata dan asosiasi'],
                ['num' => 4, 'name' => 'Worksheet Maze', 'desc' => 'Menemukan jalan keluar dari labirin sederhana di kertas.', 'moral' => 'Berpikir logis dan ketelitian'],
                ['num' => 5, 'name' => 'Worksheet Pola', 'desc' => 'Melanjutkan pola gambar atau warna yang sudah dimulai.', 'moral' => 'Pengenalan pola'],
                ['num' => 6, 'name' => 'Worksheet Menulis Kalimat', 'desc' => 'Menulis kalimat sederhana berdasarkan gambar yang diberikan.', 'moral' => 'Menulis dan literasi'],
                ['num' => 7, 'name' => 'Worksheet Silang Kata Mini', 'desc' => 'Menyelesaikan silang kata sederhana dengan gambar petunjuk.', 'moral' => 'Kosakata dan logika'],
                ['num' => 8, 'name' => 'Worksheet Gambar Bebas', 'desc' => 'Menggambar bebas berdasarkan tema yang diberikan guru.', 'moral' => 'Kreativitas dan ekspresi'],
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
Buatlah {$count} ide untuk konten bertipe "worksheet" (Buat ide worksheet edukatif dengan soal dan aktivitas yang menarik.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten worksheet.

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
