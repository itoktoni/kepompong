<?php

namespace App\Services\IdeaGenerator;

class ProyekKreatifIdea extends BaseIdea
{
    protected function typeName(): string { return 'proyek_kreatif'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Proyek Kreatif & Seni',
            'items' => [
                ['num' => 1, 'name' => 'Kolase Daun', 'desc' => 'Membuat gambar hewan atau pemandangan dari potongan daun kering.', 'moral' => 'Kreativitas dari bahan alam'],
                ['num' => 2, 'name' => 'Origami Hewan', 'desc' => 'Melipat kertas menjadi bentuk hewan sederhana.', 'moral' => 'Kesabaran dan ketelitian'],
                ['num' => 3, 'name' => 'Lukisan Jari', 'desc' => 'Menggambar menggunakan jari tangan dengan cat air.', 'moral' => 'Ekspresi diri melalui seni'],
                ['num' => 4, 'name' => 'Robot dari Kardus', 'desc' => 'Membuat robot sederhana dari kardus bekas dan tutup botol.', 'moral' => 'Daur ulang dan imajinasi'],
                ['num' => 5, 'name' => 'Mozaik Kertas', 'desc' => 'Menempel potongan kertas warna membentuk gambar indah.', 'moral' => 'Detail dan perencanaan'],
                ['num' => 6, 'name' => 'Cap Kentang', 'desc' => 'Membuat pola stamp dari kentang dan mencetaknya di kertas.', 'moral' => 'Seni cetak sederhana'],
                ['num' => 7, 'name' => 'Kerajinan Tanah Liat', 'desc' => 'Membentuk mangkuk atau hewan dari tanah liat.', 'moral' => 'Motorik halus dan kreativitas'],
                ['num' => 8, 'name' => 'Mural Kelas', 'desc' => 'Bergotong royong menghias dinding kelas dengan gambar tema tertentu.', 'moral' => 'Kerja sama dan kepemilikan bersama'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(200, $count));

        $systemPrompt = 'Kamu adalah generator ide proyek kreatif dan seni untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah TEPAT {$count} ide proyek kreatif dan seni yang UNIK untuk anak, berdasarkan tema: {$themeList}

Setiap ide HARUS berbeda — jenis proyek yang berbeda, bahan yang berbeda, dan hasil yang berbeda.

ATURAN PENTING:
- Buat TEPAT {$count} item, tidak kurang tidak lebih
- Setiap item HARUS UNIK (tidak ada duplikat)
- Proyek harus MUDAH dibuat anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan bahan mudah didapat: kertas, kardus, daun, tanah liat, krayon, botol bekas, kain perca
- Setiap ide HARUS punya langkah-langkah spesifik
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona

Contoh yang BENAR:
- "Kolase Daun Kering | membuat gambar kupu-kupu dari potongan daun kering dan lem"
- "Origami Ikan Koi | melipat kertas menjadi ikan koi dengan ekor mengembang"
- "Robot dari Kardus Bekas | membuat robot setinggi 30cm dari kardus dan tutup botol"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Proyek | Bahan Utama | Deskripsi singkat",
    "fakta": "Detail cara membuat proyek (3-5 kalimat spesifik dengan langkah-langkah)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
