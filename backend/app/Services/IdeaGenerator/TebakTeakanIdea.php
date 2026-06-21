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

        $systemPrompt = 'Kamu adalah generator ide tebak-tebakan untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide tebak-tebakan untuk anak, berdasarkan tema: {$themeList}

ATURAN PENTING:
- Tebakan harus MUDAH ditebak anak usia {$ages[0] ?? 3}-{$ages[1] ?? 8} tahun
- Gunakan clue sederhana: hewan, buah, benda, profesi, emosi
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Ide harus BERUPA TEBAK-TEBakan dengan clue dan jawaban

Contoh yang BENAR:
- "Tebak Binatang > clue: punya ekor panjang, hidup di hutan"
- "Tebak Buah > clue: warna kuning, rasa manis asam"
- "Tebak Profesi > clue: memakai jas, bekerja di kantor"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Jenis Tebakan > Kategori > Deskripsi singkat",
    "fakta": "Clue dan jawaban tebak-tebakan (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
