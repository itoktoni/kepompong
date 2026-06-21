<?php

namespace App\Services\IdeaGenerator;

class WorksheetIdea extends BaseIdea
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

        $systemPrompt = 'Kamu adalah generator ide worksheet edukatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide worksheet edukatif untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Worksheet harus SESUAI usia anak {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan format: mengisi, mencocokkan, melengkapi, mewarnai, menulis
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA WORKSHEET dengan instruksi jelas

Contoh yang BENAR:
- "Mewarnai Huruf > huruf A-Z dengan gambar objek"
- "Cocokkan Hewan > gambar hewan dengan nama hewan"
- "Isi Angka > melengkapi urutan angka"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Worksheet > Topik > Deskripsi singkat",
    "fakta": "Detail isi worksheet (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
