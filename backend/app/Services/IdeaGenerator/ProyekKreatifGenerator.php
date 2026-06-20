<?php

namespace App\Services\IdeaGenerator;

class ProyekKreatifGenerator extends BaseIdeaGenerator
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
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "proyek_kreatif" (Buat ide proyek seni dan kerajinan dengan bahan yang mudah didapat.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten proyek_kreatif.

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
