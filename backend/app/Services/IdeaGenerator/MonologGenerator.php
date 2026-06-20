<?php

namespace App\Services\IdeaGenerator;

class MonologGenerator extends BaseIdeaGenerator
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

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "monolog" (Buat ide naskah monolog dengan karakter dan tema yang relate dengan anak.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten monolog.

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
