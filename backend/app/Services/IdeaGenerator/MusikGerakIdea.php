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

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null, int $pages = 9): array
    {
        $count = max(1, min(200, $count));

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = <<<PROMPT
Kamu adalah kreator aktivitas musik dan gerak untuk anak Indonesia.
Buat ide aktivitas musik/gerak yang menyenangkan dan sesuai tema.
Gunakan HANYA bahasa Indonesia sederhana untuk anak usia {$minAge}-{$maxAge} tahun.
Output dalam format JSON array.
PROMPT;

        $themeList = $theme ?: 'musik dan gerak anak';
        $skillLine = !empty($skills) ? "\nSkill fokus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nKonteks agama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatkan EXACTLY {$count} ide aktivitas musik dan gerak berdasarkan tema: {$themeList}

PENTING:
- Jika tema menyebut nama karakter, GUNAKAN dalam judul aktivitas
- Jika tema punya situasi/setting, JADIKAN konteks aktivitas
- Setiap ide harus berbeda: beda gerakan, beda alat musik, beda cara bermain

Setiap ide harus punya 3 field:
- topik: nama aktivitas yang catchy (satu kalimat pendek)
- fakta: deskripsi LENGKAP aktivitas: di mana, siapa yang terlibat, alat apa yang dipakai, gerakan apa yang dilakukan, bagaimana cara mainnya
- moral: nilai yang dipelajari (satu kalimat)

{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Nama aktivitas",
    "fakta": "Deskripsi lengkap: latar, peserta, alat, gerakan, cara main",
    "moral": "Nilai yang dipelajari"
  }
]

HANYA output JSON. Semua teks dalam bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
