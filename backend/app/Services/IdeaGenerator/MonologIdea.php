<?php

namespace App\Services\IdeaGenerator;

class MonologIdea extends BaseIdea
{
    protected function typeName(): string { return 'monolog'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Monolog Anak',
            'items' => [
                ['num' => 1, 'name' => 'Cerita Liburan', 'desc' => 'Anak bercerita tentang pengalaman liburan terbaiknya di depan kelas.', 'moral' => 'Keberanian berbicara di depan umum'],
                ['num' => 2, 'name' => 'Pidato Mini', 'desc' => 'Anak menyampaikan pendapat tentang topik sederhana dalam 1 menit.', 'moral' => 'Menyampaikan ide dengan jelas'],
                ['num' => 3, 'name' => 'Bercerita dari Benda', 'desc' => 'Anak memilih satu benda dan bercerita tentangnya secara imajinatif.', 'moral' => 'Kreativitas dan improvisasi'],
                ['num' => 4, 'name' => 'Stand Up Comedy Anak', 'desc' => 'Anak menceritakan hal lucu yang pernah dialaminya.', 'moral' => 'Humor dan kepercayaan diri'],
                ['num' => 5, 'name' => 'Presenter Berita', 'desc' => 'Anak membacakan berita singkat tentang kejadian di sekolah.', 'moral' => 'Kemampuan membaca dan presentasi'],
                ['num' => 6, 'name' => 'Review Buku', 'desc' => 'Anak menceritakan buku favoritnya dan merekomendasikannya.', 'moral' => 'Literasi dan berbagi rekomendasi'],
                ['num' => 7, 'name' => 'Ceritakan Gambar', 'desc' => 'Anak menceritakan apa yang terjadi dalam gambar tanpa menulis.', 'moral' => 'Observasi dan narasi'],
                ['num' => 8, 'name' => 'Terima Kasih', 'desc' => 'Anak menyampaikan rasa terima kasih kepada orang spesial dalam hidupnya.', 'moral' => 'Menghargai orang lain'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));

        $systemPrompt = 'Kamu adalah generator ide monolog untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide monolog untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Monolog harus MUDAH dipahami dan diucapkan anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan topik sederhana: cerita pengalaman, pendapat, perasaan
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA TOPIK MONLOG yang bisa diceritakan

Contoh yang BENAR:
- "Cerita Liburan > bercerita tentang pengalaman liburan"
- "Pidato Mini > menyampaikan pendapat tentang topik sederhana"
- "Bercerita dari Benda > memilih benda dan bercerita tentangnya"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Monolog > Topik > Deskripsi singkat",
    "fakta": "Detail cara melakukan monolog (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
