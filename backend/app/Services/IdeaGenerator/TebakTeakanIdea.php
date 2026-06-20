<?php

namespace App\Services\IdeaGenerator;

class TebakTeakanIdea extends BaseIdea
{
    protected function typeName(): string { return 'tebak_teakan'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Tebak-tebakan',
            'items' => [
                ['num' => 1, 'name' => 'Tebak Binatang', 'desc' => 'Guru memberikan clue tentang hewan, anak menebak hewan apa.', 'moral' => 'Pengetahuan tentang hewan'],
                ['num' => 2, 'name' => 'Tebak Buah', 'desc' => 'Anak menebak buah dari deskripsi bentuk, warna, dan rasanya.', 'moral' => 'Pengenalan buah-buahan'],
                ['num' => 3, 'name' => 'Tebak Profesi', 'desc' => 'Menirukan gerakan profesi, teman menebak profesi apa.', 'moral' => 'Mengenal berbagai profesi'],
                ['num' => 4, 'name' => 'Tebak Suara', 'desc' => 'Memutar suara henda/alat, anak menebak sumber suara.', 'moral' => 'Pendengaran dan asosiasi'],
                ['num' => 5, 'name' => 'Tebak Lagu', 'desc' => 'Guru bersenandung lagu, anak menebak judul lagu.', 'moral' => 'Mengenal lagu dan musik'],
                ['num' => 6, 'name' => 'Tebak Emosi', 'desc' => 'Menunjukkan ekspresi wajah, anak menebak emosi apa yang dirasakan.', 'moral' => 'Empati dan pengenalan emosi'],
                ['num' => 7, 'name' => 'Tebak Benda', 'desc' => 'Anak meraba benda dalam kantong tertutup dan menebaknya.', 'moral' => 'Sensorik dan deduksi'],
                ['num' => 8, 'name' => 'Tebak Cerita', 'desc' => 'Guru menceritakan awal cerita, anak menebak kelanjutannya.', 'moral' => 'Imajinasi dan prediksi'],
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
Buatlah {$count} ide untuk konten bertipe "tebak_teakan" (Buat ide tebak-tebakan dengan clue dan jawaban yang menarik.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten tebak_teakan.

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
