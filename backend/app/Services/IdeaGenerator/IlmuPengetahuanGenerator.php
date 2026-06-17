<?php

namespace App\Services\IdeaGenerator;

class IlmuPengetahuanGenerator extends BaseIdeaGenerator
{
    protected function typeName(): string
    {
        return 'ilmu_pengetahuan';
    }

    public function generate(): array
    {
        return [
            'title' => 'Ide Ilmu Pengetahuan',
            'items' => [
                ['num' => 1, 'name' => 'Vulkanizer Sederhana', 'desc' => 'Membuat gunung berapi mini dari baking soda dan cuka.', 'moral' => 'Reaksi kimia dasar yang menyenangkan'],
                ['num' => 2, 'name' => 'Pelangi dalam Gelas', 'desc' => 'Menyusun air berwarna berbeda berdasarkan densitasnya.', 'moral' => 'Belajar tentang massa jenis'],
                ['num' => 3, 'name' => 'Tanaman Kacang', 'desc' => 'Mengamati pertumbuhan kacang dari biji hingga tumbuh tunas.', 'moral' => 'Kesabaran dan siklus kehidupan'],
                ['num' => 4, 'name' => 'Magnet Ajaib', 'desc' => 'Menguji benda mana yang bisa ditarik magnet.', 'moral' => 'Eksplorasi sifat material'],
                ['num' => 5, 'name' => 'Bayangan Matahari', 'desc' => 'Mengamati pergerakan bayangan sepanjang hari.', 'moral' => 'Memahami rotasi bumi'],
                ['num' => 6, 'name' => 'Kupu-kupu dari Ulat', 'desc' => 'Mengamati metamorfosis kupu-kupu dari ulat hingga sayap.', 'moral' => 'Keajaiban siklus hidup'],
                ['num' => 7, 'name' => 'Teleskop Botol', 'desc' => 'Membuat teleskop sederhana dari botol plastik.', 'moral' => 'Kreativitas dalam sains'],
                ['num' => 8, 'name' => 'Cuaca Harian', 'desc' => 'Mencatat cuaca setiap hari dan membuat grafik sederhana.', 'moral' => 'Observasi dan pencatatan data'],
            ],
        ];
    }

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = "You are a children's science education designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} science activity ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Generate simple science experiments and activities that spark curiosity and teach basic scientific concepts.\n";
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

        return $this->aiGenerate($systemPrompt, 'Buatkan ide aktivitas ilmu pengetahuan untuk anak', $count, $theme);
    }
}
