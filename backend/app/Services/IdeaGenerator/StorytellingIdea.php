<?php

namespace App\Services\IdeaGenerator;

class StorytellingIdea extends BaseIdea
{
    protected function typeName(): string { return 'storytelling'; }

    public function generate(): array
    {
        return [
            'title' => 'Ide Aktivitas Story Telling',
            'items' => [
                ['num' => 1, 'name' => 'Cerita Berantai', 'desc' => 'Setiap anak menambahkan satu kalimat untuk melanjutkan cerita secara bergiliran.', 'moral' => 'Melatih imajinasi dan kemampuan bercerita'],
                ['num' => 2, 'name' => 'Dongeng dengan Boneka', 'desc' => 'Anak menggunakan boneka tangan untuk menceritakan dongeng favorit mereka.', 'moral' => 'Mengekspresikan diri melalui peran'],
                ['num' => 3, 'name' => 'Cerita dari Gambar', 'desc' => 'Anak melihat serangkaian gambar dan menyusunnya menjadi cerita yang kreatif.', 'moral' => 'Melatih berpikir logis dan kreatif'],
                ['num' => 4, 'name' => 'Mendongeng Interaktif', 'desc' => 'Guru bercerita dan anak-anak ikut menirukan suara atau gerakan tokoh.', 'moral' => 'Mendengarkan dengan aktif dan antusias'],
                ['num' => 5, 'name' => 'Cerita Imajinasi', 'desc' => 'Anak menciptakan dunia imajinasi sendiri dan menceritakannya kepada teman.', 'moral' => 'Imajinasi tanpa batas membuka wawasan'],
                ['num' => 6, 'name' => 'Sulih Suara Cerita', 'desc' => 'Anak memberikan suara untuk karakter dalam cerita yang dibacakan guru.', 'moral' => 'Mengasah ekspresi dan intonasi'],
                ['num' => 7, 'name' => 'Teater Mini', 'desc' => 'Anak-anak memerankan adegan singkat dari cerita yang sudah dibacakan.', 'moral' => 'Keberanian tampil di depan teman'],
                ['num' => 8, 'name' => 'Cerita Bergambar', 'desc' => 'Anak menggambar adegan favorit lalu menceritakannya kepada kelompok.', 'moral' => 'Menuangkan ide dalam bentuk visual dan verbal'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null, int $pages = 9): array
    {
        $count = max(1, min(200, $count));

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = <<<PROMPT
Kamu adalah penulis cerita anak Indonesia profesional.
Buat ide cerita anak yang menarik dan sesuai tema yang diberikan.
Gunakan HANYA bahasa Indonesia sederhana untuk anak usia {$minAge}-{$maxAge} tahun.
TIDAK BOLEH gunakan karakter aneh (huruf Cina, Jepang, Arab, simbol aneh).
HANYA gunakan huruf Latin A-Z, angka, dan tanda baca standar.
Jangan gunakan kata sulit atau bahasa asing.
Output dalam format JSON array.
PROMPT;

        $themeList = $theme ?: 'petualangan anak Indonesia';
        $skillLine = !empty($skills) ? "\nSkill/nilai yang harus diajarkan: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nKonteks agama: {$agama}" : '';

        $userPrompt = <<<PROMPT
Buatkan EXACTLY {$count} ide cerita anak berdasarkan tema berikut:

TEMA: {$themeList}

PENTING:
- Jika tema menyebut nama anak/karakter (contoh: faqih), GUNAKAN nama itu sebagai tokoh utama
- Jika tema menyebut situasi (contoh: mancing di laut, gagal tapi pantang menyerah), JADIKAN itu alur cerita
- Jika tema menyebut tempat (contoh: laut, gunung, sekolah), JADIKAN itu latar cerita
- Setiap ide harus berbeda: beda konflik, beda setting, beda karakter sampingan

Setiap ide cerita harus punya 3 field:
- topik: judul cerita yang menarik dan catchy (satu kalimat pendek)
- fakta: rencana cerita LENGKAP ditulis dalam kalimat natural mengalir. TIDAK BOLEH gunakan format "Tokoh:", "Latar:", "Alur:", "Ending:" atau tanda kurung (). Tulis seperti bercerita biasa.
- moral: pelajaran moral dari cerita (satu kalimat)

CONTOH output yang BENAR (jika tema tentang anak mancing):
- topik: "Faqih dan Ikan Marlin yang Ajaib"
- fakta: "Pagi hari di pantai selatan Jawa, Faqih diajak Kakek yang sudah berpengalaman memancing. Mereka mancing seharian tapi tidak dapat ikan sama sekali. Faqih hampir menyerah tapi Kakek mengajarkan mengganti umpan dari cacing ke udang. Tiba-tiba kail ditarik sangat kuat oleh ikan marlin besar! Faqih dan Kakek bekerja sama menarik ikan itu. Akhirnya Faqih berhasil dan belajar bahwa kesabaran membuahkan hasil."
- moral: "Kesabaran dan pantang menyerah akan membuahkan hasil."

{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Judul cerita",
    "fakta": "Rencana cerita lengkap dalam kalimat natural mengalir",
    "moral": "Pelajaran moral"
  }
]

HANYA output JSON. Semua teks dalam bahasa Indonesia.
PROMPT;

        return $this->aiGenerate($systemPrompt, $userPrompt, $count, $theme);
    }
}
