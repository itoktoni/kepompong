<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class BermainPeranGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? $input['topic'] ?? '';
        $ideaDesc = $input['desc'] ?? $input['fakta'] ?? '';
        $ideaMoral = $input['moral'] ?? '';
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;
        $pagesCount = max(1, min(24, $input['pages'] ?? 8));

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: balita usia 1-3 tahun. Gunakan kalimat SANGAT PENDEK (3-6 kata per halaman). 1 kalimat per halaman.",
            $maxAge <= 6 => "Target: anak usia 4-6 tahun. Gunakan kalimat pendek (5-10 kata per halaman). Skenario sederhana dengan urutan jelas.",
            default => "Target: anak usia 7-10 tahun. Gunakan kalimat lebih panjang (10-20 kata per halaman). Skenario lebih detail dengan banyak adegan.",
        };

        $themeInput = $theme ?: 'bermain peran seru';

        $systemPrompt = "Kamu adalah penulis skenario bermain peran untuk anak Indonesia.\n";
        $systemPrompt .= "PENTING: Kamu HARUS membuat TEPAT {$pagesCount} halaman.\n";
        $systemPrompt .= "PENTING: Gunakan HANYA bahasa Indonesia dengan alfabet Latin. Jangan karakter non-Latin. Jangan emoji.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "Format: Profesi/Situasi > Lokasi > Deskripsi skenario\n";
        $systemPrompt .= "- Skenario harus MUDAH dimainkan anak\n";
        $systemPrompt .= "- Gunakan profesi/situasi yang familiar: dokter, koki, guru, polisi, astronot, dll\n";
        $systemPrompt .= "- JANGAN gunakan 'si' di judul\n";
        $systemPrompt .= "- JANGAN gunakan nama karakter/persona\n";
        $systemPrompt .= "- Ide harus GLOBAL, berupa skenario bermain peran yang bisa dimainkan\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},..exactly {$pagesCount} items]}\n";
        $systemPrompt .= "- Tema: {$themeInput}\n";
        $systemPrompt .= "- Setiap halaman berisi SATU adegan atau dialog (MAKS 40 kata)\n";
        $systemPrompt .= "PENTING: Gunakan HANYA kata sederhana bahasa Indonesia. DILARANG: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable.\n";

        $userPrompt = "Buatkan skenario bermain peran untuk anak tentang tema: {$themeInput}\n\n";
        if ($ideaDesc) {
            $userPrompt .= "Konteks skenario dari ide (ikuti dan kembangkan):\n{$ideaDesc}\n\n";
        }
        if ($ideaMoral) {
            $userPrompt .= "Pelajaran moral yang harus muncul: {$ideaMoral}\n\n";
        }
        $userPrompt .= "Jumlah halaman: {$pagesCount}\n";
        $userPrompt .= "Usia: {$minAge}-{$maxAge} tahun\n\n";
        $userPrompt .= "ATURAN PENTING:\n";
        $userPrompt .= "- JANGAN gunakan 'si' di judul\n";
        $userPrompt .= "- JANGAN gunakan nama karakter/persona\n";
        $userPrompt .= "- Format: Profesi/Situasi > Lokasi > Deskripsi skenario\n";
        $userPrompt .= "- Setiap halaman = satu adegan atau dialog bermain peran\n";
        $userPrompt .= "- Gunakan konteks Indonesia\n\n";
        $userPrompt .= "Contoh yang BENAR:\n";
        $userPrompt .= '- "Dokter Hewan > Klinik > Merawat hewan sakit"\n';
        $userPrompt .= '- "Koki Sushi > Restoran > Membuat sushi imajinasi untuk pelanggan"\n\n';
        $userPrompt .= "Output dalam format JSON:\n";
        $userPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},..exactly {$pagesCount} items]}\n\n";
        $userPrompt .= "Hanya output JSON. Semua teks bahasa Indonesia sederhana.";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title']) || empty($result['pages'])) {
                return $this->fallback($theme, $ideaDesc, $ideaMoral, $pagesCount);
            }

            $pages = array_slice($result['pages'], 0, $pagesCount);
            $renumbered = [];
            foreach ($pages as $index => $page) {
                $renumbered[] = [
                    'num' => $index + 1,
                    'text' => $this->cleanText($page['text'] ?? (is_string($page) ? $page : '')),
                ];
            }

            return [
                'title' => $this->cleanText($result['title']),
                'desc' => $this->cleanText($result['desc'] ?? $ideaDesc),
                'moral' => $this->cleanText($result['moral'] ?? $ideaMoral),
                'pages' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            return $this->fallback($theme, $ideaDesc, $ideaMoral, $pagesCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        foreach ($result['pages'] as $index => $page) {
            if ($index === 0) continue;
            $pages[] = ['num' => $index, 'text' => $page['text'] ?? ''];
        }

        return array_merge($this->baseActivityData('bermain_peran', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages],
        ]);
    }

    public function assetConfig(): array
    {
        return [
            'mode'          => 'grid',
            'default_pages' => 8,
            'image_size'    => '2K',
            'style'         => 'Modern pixar 3D cartoon, bright colorful daylight, kid friendly.',
            'extra_rules'   => "- No speech bubbles allowed\n- No written text in panels except cover",
        ];
    }

    public function buildPrompt(array $result, array $input): string
    {
        $pages = $result['pages'];
        $count = count($pages);
        $title = $result['title'];
        $desc = $result['desc'] ?? '';
        $moral = $result['moral'] ?? '';
        $grid = $this->gridLabel($count);
        $panel = $count - 1;

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, colorful kid-friendly illustration representing the role-play scenario."];
        foreach ($pages as $i => $p) {
            if ($i === 0) continue;
            $lines[] = "Page {$i}: {$p['text']}";
        }

        $p = "A {$count}-panel comic page storyboard, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\nMoral: {$moral}\n\n";
        $p .= "Each panel is an illustration for the role-play scene:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- Cover title is not too big and not too small\n";
        $p .= "- Page 1-{$panel} is role-play scene\n";
        $p .= $this->commonRules();

        return $p;
    }

    private function fallback(string $theme, string $ideaDesc, string $ideaMoral, int $pagesCount): array
    {
        $title = 'Bermain Peran ' . ucfirst($theme);
        $pages = [];
        for ($i = 1; $i <= $pagesCount; $i++) {
            $pages[] = ['num' => $i, 'text' => "Adegan {$i} dari bermain peran tentang {$theme}"];
        }
        return ['title' => $title, 'desc' => $ideaDesc, 'moral' => $ideaMoral, 'pages' => $pages];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
