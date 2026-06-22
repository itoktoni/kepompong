<?php

namespace App\Services\IdeaGenerator;

class MusikGerakIdea extends BaseIdea
{
    protected function typeName(): string { return 'musik_gerak'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Musik & Gerak',
            'items' => [
                ['num' => 1, 'name' => 'Tarian Kompak', 'desc' => 'Semua anak menari bersama mengikuti irama musik yang ceria.', 'moral' => 'Kekompakan dan ritme'],
                ['num' => 2, 'name' => 'Band Sederhana', 'desc' => 'Anak-anak bermain alat musik dari benda bekas secara bersamaan.', 'moral' => 'Harmoni dan kerja sama'],
                ['num' => 3, 'name' => 'Freeze Dance', 'desc' => 'Menari saat musik berhenti, berhenti saat musik berhenti.', 'moral' => 'Mendengarkan dan refleks'],
                ['num' => 4, 'name' => 'Marching Band Mini', 'desc' => 'Berbaris sambil memukul drum dari ember dan kaleng.', 'moral' => 'Disiplin dan koordinasi'],
                ['num' => 5, 'name' => 'Bernyanyi Bersama', 'desc' => 'Menyanyikan lagu daerah sambil tepuk tangan berirama.', 'moral' => 'Mengenal budaya dan musik'],
                ['num' => 6, 'name' => 'Gerak dan Lagu', 'desc' => 'Menggerakkan tubuh sesuai lirik lagu anak yang dinyanyikan.', 'moral' => 'Koordinasi tubuh dan musik'],
                ['num' => 7, 'name' => 'Ritme Tubuh', 'desc' => 'Menciptakan irama menggunakan tepuk tangan, ketuk kaki, dan jentik jari.', 'moral' => 'Kreativitas musikal'],
                ['num' => 8, 'name' => 'Parade Musik', 'desc' => 'Berjalan berkeliling sekolah sambil memainkan alat musik sederhana.', 'moral' => 'Kebanggaan dan penampilan'],
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
Generate EXACTLY {$count} UNIQUE ideas for "musik_gerak" (music and movement) content type, based on theme: {$themeList}

Each idea MUST be a DIFFERENT music/movement activity.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- DO NOT use "si" in titles
- DO NOT use character/person names
- DO NOT include location/place names in the topik field
- topik: just the activity name only, e.g. "Tarian Kompak", "Freeze Dance", "Ritme Tubuh"
- fakta: a comma-separated list of EXACTLY 10 attractive children's music/movement title ideas. Each title must be catchy, fun, and child-friendly.
- moral: factual information about the activity (movements involved, equipment, skills trained)

CORRECT examples:
- topik: "Tarian Kompak"
- fakta: "Tarian Kompak yang Seru, Ayo Menari Bersama!, Tarian Kompak Gembira, Si Penari Cilik, Petualangan Tarian, Tarian Kompak dan Ceria, Rahasia Tarian Kompak, Tarian Kompak Sahabat, Si Lincah Menari, Tarian Kompak Hebat"
- moral: "Semua anak menari bersama mengikuti irama musik. Melatih koordinasi tubuh dan ritme. Bisa pakai musik daerah."

{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Activity name only",
    "fakta": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's music/movement titles)",
    "moral": "Factual information about the music/movement activity"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
