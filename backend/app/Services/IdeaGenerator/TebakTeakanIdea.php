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
        $count = max(1, min(200, $count));

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas for "tebak_teakan" (guessing game) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT guessing game.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the game name only, e.g. "Tebak Binatang", "Tebak Buah", "Tebak Suara"
- fakta: a comma-separated list of EXACTLY 10 attractive children's guessing game title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the game (how to play, clues format, skills trained)

CORRECT examples:
- topik: "Tebak Binatang"
- fakta: "Tebak Binatang yang Seru, Ayo Tebak Binatang!, Si Pintar Tebak Hewan, Petualangan Tebak Binatang, Tebak Binatang Ajaib, Rahasia Binatang Tersembunyi, Tebak Binatang Lucu, Si Cepat Tebak Hewan, Tebak Binatang dari Suara, Tebak Binatang dan Menang"
- moral: "Guru memberikan clue tentang hewan, anak menebak hewan apa. Melatih pengetahuan tentang hewan dan daya ingat."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Game name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's guessing game titles)",
    "moral": "Factual information about the guessing game"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
