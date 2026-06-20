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

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Output harus dalam format JSON array.';

        $themeList = $theme ?: '';
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "musik_gerak" (Buat ide lagu dan gerakan untuk anak dengan lirik sederhana.), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten musik_gerak.

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
