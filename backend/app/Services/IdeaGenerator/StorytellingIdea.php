<?php

namespace App\Services\IdeaGenerator;

class StorytellingIdea extends BaseIdea
{
    protected function typeName(): string { return 'storytelling'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Story Telling',
            'items' => [
                ['num' => 1, 'name' => 'Cerita Berantai', 'desc' => 'Setiap anak menambahkan satu kalimat untuk melanjutkan cerita secara bergiliran.', 'moral' => 'Melatih imajinasi dan kemampuan bercerita'],
                ['num' => 2, 'name' => 'Dongeng dengan Boneka', 'desc' => 'Anak menggunakan boneka tangan untuk menceritakan dongeng favorit mereka.', 'moral' => 'Mengekspresikan diri melalui peran'],
                ['num' => 3, 'name' => 'Cerita dari Gambar', 'desc' => 'Anak melihat serangkaian gambar dan menyusunnya menjadi cerita yang kreatif.', 'moral' => 'Melatih berpikir logis dan kreatif'],
                ['num' => 4, 'name' => 'Mendongeng Interaktif', 'desc' => 'Guru bercerita dan anak-anak ikut menirukan suara atau gerakan tokoh.', 'moral' => 'Mendengarkan dengan aktif dan antusias'],
                ['num' => 5, 'name' => 'Cerita Imajinasi', 'desc' => 'Anak menciptakan dunia imajinasi sendiri dan menceritakannya kepada teman.', 'moral' => 'Imajinasi tanpa batas membuka wawasan'],
                ['num' => 6, 'name' => 'Sulih Suara Cerita', 'desc' => 'Anak memberikan suara untuk karakter dalam cerita yang dibacakan guru.', 'moral' => 'Mengasah ekspresi dan intonasi'],
                ['num' => 7, 'name' => 'Teater Mini', 'desc' => 'Anak-anak memerankan adegan singkat dari cerita yang sudah dibacakan.', 'moral' => 'Keberanian tampil di depan teman'],
                ['num' => 8, 'name' => 'Cerita Bergambar', 'desc' => 'Anak menggambar adegan favorit lalu menceritakannya kepada kelompok.', 'moral' => 'Menuangkan ide dalam bentuk visual dan verbal'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(50, $count));

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain apalagi china seperti 它的. JANGAN gunakan kata-kata sulit/bahasa asing seperti: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Gunakan kata sederhana: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "storytelling" (Buat ide cerita dengan karakter utama, konflik, dan penyelesaian.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten storytelling.

ATURAN PENTING:
- JANGAN gunakan "si" di judul (contoh SALAH: "Raja si Paus", BENAR: "Paus Sperma di Laut Banda")
- JANGAN gunakan nama karakter/persona (contoh SALAH: "Sari si Paus", BENAR: "Paus Sperma di Laut Banda")
- Ide harus GLOBAL, bukan cerita spesifik dengan tokoh
- Format: Hewan/Objek > Tempat > Fakta spesifik

Contoh yang BENAR:
- "Paus Sperma > Laut Banda > bisa menyelam hingga 3 kilometer untuk mencari makanan di kedalaman laut"
- "Ikan Mola-mola > Nusa Penida > ikan terberat di dunia yang bisa mencapai 2 ton, suka berjemur di permukaan laut"
- "Pari Manta > Raja Ampat > bisa terbang melompat keluar air, sayapnya bisa mencapai 7 meter"

Contoh yang SALAH (JANGAN ikuti):
- "Raja si Paus Sperma yang Bisa Menyelam" (ada "si")
- "Sari si Penyanyi Paus" (ada nama karakter)

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
