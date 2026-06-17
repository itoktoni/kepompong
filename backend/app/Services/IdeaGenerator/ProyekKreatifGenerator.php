<?php

namespace App\Services\IdeaGenerator;

class ProyekKreatifGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'proyek_kreatif';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Proyek Kreatif & Seni',
            'items' => [
                ['num' => 1, 'name' => 'Kolase Daun', 'desc' => 'Membuat gambar hewan atau pemandangan dari potongan daun kering.', 'moral' => 'Kreativitas dari bahan alam'],
                ['num' => 2, 'name' => 'Origami Hewan', 'desc' => 'Melipat kertas menjadi bentuk hewan sederhana.', 'moral' => 'Kesabaran dan ketelitian'],
                ['num' => 3, 'name' => 'Lukisan Jari', 'desc' => 'Menggambar menggunakan jari tangan dengan cat air.', 'moral' => 'Ekspresi diri melalui seni'],
                ['num' => 4, 'name' => 'Robot dari Kardus', 'desc' => 'Membuat robot sederhana dari kardus bekas dan tutup botol.', 'moral' => 'Daur ulang dan imajinasi'],
                ['num' => 5, 'name' => 'Mozaik Kertas', 'desc' => 'Menempel potongan kertas warna membentuk gambar indah.', 'moral' => 'Detail dan perencanaan'],
                ['num' => 6, 'name' => 'Cap Kentang', 'desc' => 'Membuat pola stamp dari kentang dan mencetaknya di kertas.', 'moral' => 'Seni cetak sederhana'],
                ['num' => 7, 'name' => 'Kerajinan Tanah Liat', 'desc'              => 'Membentuk mangkuk atau hewan dari tanah liat.', 'moral' => 'Motorik halus dan kreativitas'],
                ['num' => 8, 'name' => 'Mural Kelas', 'desc' => 'Bergotong royong menghias dinding kelas dengan gambar tema tertentu.', 'moral' => 'Kerja sama dan kepemilikan bersama'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's creative project and art designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} creative project ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate art and craft projects that develop creativity, fine motor skills, and aesthetic appreciation.\n";
        $systemPrompt .= $this->buildAgeGuide($maxAge) . "\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"items\":[{\"name\":\"...\",\"desc\":\"...\",\"moral\":\"...\"},...]}\n";
        $systemPrompt .= "- Each desc max 100 chars, moral max 60 chars\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge}\n";

        if ($agama) {
            $systemPrompt .= "- Religion: {$agama}\n" . $this->buildAgamaGuide($agama) . "\n";
        }
        if (!empty($skills)) {
            $systemPrompt .= "- Skills to focus on: " . implode(', ', $skills) . "\n";
        }

        $systemPrompt .= "CRITICAL: This content is for CHILDREN. Use ONLY safe, kind, positive language.\n";

        return $this->aiGenerate($systemPrompt, 'Buatkan ide proyek kreatif dan seni untuk anak', $count, $theme);
    }
}
