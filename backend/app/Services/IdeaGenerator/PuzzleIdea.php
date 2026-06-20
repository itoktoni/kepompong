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

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "puzzle" (Buat puzzle dan teka-teki yang melatih logika anak.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten puzzle.

ATURAN PENTING:
- JANGAN gunakan "si" di judul (contoh SALAH: "Raja si Paus", BENAR: "Paus Sperma di Laut Banda")
- JANGAN gunakan nama karakter/persona (contoh SALAH: "Sari si Paus", BENAR: "Paus Sperma di Laut Banda")
- Ide harus GLOBAL, bukan cerita spesifik dengan tokoh
- Format: Hewan/Objek > Tempat > Fakta spesifik

Contoh yang BENAR:
- "Paus Sperma > Laut Banda > bisa menyelam hingga 3 kilometer untuk mencari makanan di kedalaman laut"
- "Ikan Mola-mola > Nusa Penida > ikan terberat di dunia yang bisa mencapai 2 ton"

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
