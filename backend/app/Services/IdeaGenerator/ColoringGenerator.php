<?php

namespace App\Services\IdeaGenerator;

class ColoringGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string { return 'coloring'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Mewarnai',
            'items' => [
                ['num' => 1, 'name' => 'Mewarnai Hewan', 'desc' => 'Anak mewarnai gambar hewan kesukaan dengan krayon atau pensil warna.', 'moral' => 'Mengenal hewan dan warna'],
                ['num' => 2, 'name' => 'Mewarnai Pemandangan', 'desc' => 'Mewarnai gambar pemandangan alam seperti gunung, pantai, atau sawah.', 'moral' => 'Menghargai keindahan alam'],
                ['num' => 3, 'name' => 'Mewarnai Buah dan Sayur', 'desc' => 'Mewarnai gambar buah dan sayuran sambil belajar nama-namanya.', 'moral' => 'Pengenalan makanan sehat'],
                ['num' => 4, 'name' => 'Mandala Sederhana', 'desc' => 'Mewarnai pola mandala sederhana untuk melatih konsentrasi.', 'moral' => 'Kesabaran dan fokus'],
                ['num' => 5, 'name' => 'Color by Number', 'desc' => 'Mewarnai gambar berdasarkan angka yang menentukan warnanya.', 'moral' => 'Berhitung sambil mewarnai'],
                ['num' => 6, 'name' => 'Mewarnai Keluarga', 'desc' => 'Menggambar dan mewarnai anggota keluarga sendiri.', 'moral' => 'Menghargai keluarga'],
                ['num' => 7, 'name' => 'Mewarnai Kebun Binatang', 'desc' => 'Mewarnai gambar kebun binatang lengkap dengan hewan-hewannya.', 'moral' => 'Pengetahuan tentang satwa'],
                ['num' => 8, 'name' => 'Mewarnai Kreasi Sendiri', 'desc' => 'Anak menggambar bebas lalu mewarnai dengan warna pilihan sendiri.', 'moral' => 'Kebebasan berekspresi'],
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
Buatlah {$count} ide untuk konten bertipe "coloring" (Buat ide halaman mewarnai dengan gambar yang menarik untuk anak.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten coloring.

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
