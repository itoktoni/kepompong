<?php

namespace App\Services\IdeaGenerator;

class LatihanOtakIdea extends BaseIdea
{
    protected function typeName(): string { return 'latihan_otak'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Latihan Otak',
            'items' => [
                ['num' => 1, 'name' => 'Hitung Cepat', 'desc' => 'Anak menjawab soal hitungan sederhana secepat mungkin.', 'moral' => 'Kecepatan berpikir dan ketepatan'],
                ['num' => 2, 'name' => 'Ingat Urutan', 'desc' => 'Guru menyebut angka/huruf, anak mengulang urutannya.', 'moral' => 'Melatih daya ingat jangka pendek'],
                ['num' => 3, 'name' => 'Cari Perbedaan', 'desc' => 'Menemukan perbedaan antara dua gambar yang mirip.', 'moral' => 'Observasi dan ketelitian'],
                ['num' => 4, 'name' => 'Kata Berantai', 'desc' => 'Menyebut kata yang dimulai huruf terakhir kata sebelumnya.', 'moral' => 'Kosakata dan kecepatan berpikir'],
                ['num' => 5, 'name' => 'Pola Warna', 'desc' => 'Melanjutkan pola warna yang diberikan guru.', 'moral' => 'Pengenalan pola dan logika'],
                ['num' => 6, 'name' => 'Math Bingo', 'desc' => 'Bingo dengan jawaban soal matematika sederhana.', 'moral' => 'Berhitung sambil bersenang-senang'],
                ['num' => 7, 'name' => 'Tebak Suara', 'desc' => 'Menebak sumber suara yang dimainkan dari rekaman.', 'moral' => 'Pendengaran dan asosiasi'],
                ['num' => 8, 'name' => 'Cerita Terbalik', 'desc' => 'Menceritakan kejadian dari akhir ke awal.', 'moral' => 'Berpikir kreatif dan berbeda'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide latihan otak dan brain training untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide latihan otak untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Latihan harus MUDAH dilakukan anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan konsep sederhana: menghitung, mengingat, mencari perbedaan, mengurutkan
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA LATIHAN OTAK yang bisa dikerjakan

Contoh yang BENAR:
- "Hitung Cepat > menjawab soal hitungan sederhana"
- "Ingat Urutan > mengulang urutan angka/huruf"
- "Cari Perbedaan > menemukan perbedaan dua gambar"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Latihan > Topik > Deskripsi singkat",
    "fakta": "Detail cara melakukan latihan (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
