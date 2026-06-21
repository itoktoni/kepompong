<?php

namespace App\Services\IdeaGenerator;

class PermainanIdea extends BaseIdea
{
    protected function typeName(): string { return 'permainan'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Permainan Seru',
            'items' => [
                ['num' => 1, 'name' => 'Tebak Kata dari Gambar', 'desc' => 'Guru menunjukkan gambar, anak menebak kata yang sesuai.', 'moral' => 'Melatih daya ingat dan keberanian menjawab'],
                ['num' => 2, 'name' => 'Simon Says', 'desc' => 'Mengikuti perintah yang dimulai dengan "Simon Says".', 'moral' => 'Melatih konsentrasi dan mendengar dengan baik'],
                ['num' => 3, 'name' => 'Teka-Teki Lucu', 'desc' => 'Anak menjawab teka-teki sederhana yang jawabannya menggelitik logika.', 'moral' => 'Berpikir kritis dan memahami sebab-akibat'],
                ['num' => 4, 'name' => 'Permainan Memori', 'desc' => 'Mencocokkan kartu dengan gambar yang sama secara berpasangan.', 'moral' => 'Melatih daya ingat dan konsentrasi'],
                ['num' => 5, 'name' => 'Estafet Kelereng', 'desc' => 'Memindahkan kelereng dengan sendok dari garis start ke finish.', 'moral' => 'Kesabaran dan koordinasi motorik'],
                ['num' => 6, 'name' => 'Bola Karaoke', 'desc' => 'Melempar bola sambil bernyanyi, yang menangkap melanjutkan lagu.', 'moral' => 'Keberanian dan kerja sama'],
                ['num' => 7, 'name' => 'Puzzle Raksasa', 'desc' => 'Menyusun potongan puzzle besar secara berkelompok.', 'moral' => 'Kerja sama dan berpikir sistematis'],
                ['num' => 8, 'name' => 'Cacing Raksasa', 'desc' => 'Berbaris memegang bahu teman depan, berjalan melewati rintangan.', 'moral' => 'Koordinasi tim dan kekompakan'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide permainan untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide permainan untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Permainan harus MUDAH dimainkan anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan aturan sederhana: tebak kata, estafet, kejar-kejaran, bingo
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA PERMAINAN dengan aturan jelas

Contoh yang BENAR:
- "Tebak Kata dari Gambar > menebak kata dari gambar"
- "Estafet Kelereng > memindahkan kelereng dengan sendok"
- "Simon Says > mengikuti perintah Simon Says"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Permainan > Aturan > Deskripsi singkat",
    "fakta": "Detail aturan cara bermain (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
