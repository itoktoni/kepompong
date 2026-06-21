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
        $count = max(1, min(20, $count));

        $systemPrompt = 'You are a music and movement idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages. DO NOT use difficult/foreign words. Output must be in JSON array format.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate {$count} music and movement ideas for children, based on theme: {$themeList}

IMPORTANT RULES:
- Activities must be EASY for children aged {$ages[0] ?? 3}-{$ages[1] ?? 8} years old
- Use simple movements: dancing, clapping, jumping, walking
- DO NOT use "si" in titles
- DO NOT use character/person names
- Ideas must be MUSIC AND MOVEMENT ACTIVITIES

CORRECT examples:
- "Tarian Kompak | menari bersama mengikuti irama musik"
- "Freeze Dance | menari saat musik berhenti"
- "Ritme Tubuh | menciptakan irama dengan tepuk tangan"

Use Indonesian context.
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "topik": "Activity Type | Movement | Short description",
    "fakta": "Details on how to perform the activity (3-5 specific sentences)",
    "moral": "Lesson that can be learned"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count);
    }
}
