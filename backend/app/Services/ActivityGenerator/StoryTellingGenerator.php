<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class StoryTellingGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? '';
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;
        $pagesCount = max(1, min(24, $input['pages'] ?? 16));

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT simple sentences (3-6 words per page). Use basic vocabulary. Focus on colors, animals, family. Each page 1 short sentence only.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short simple sentences (5-10 words per page). Simple story with clear sequence.",
            default => "Target: older children ages 7-10. Use longer sentences (10-20 words per page). Write a RICH detailed story with MANY scenes and locations.",
        };

        $themeInput = $theme ?: 'penting untuk anak';

        $systemPrompt = "You are a children's story writer for Indonesia.\n";
        $systemPrompt .= "CRITICAL: You MUST write EXACTLY {$pagesCount} pages.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "Format: Hewan/Objek > Lokasi > fakta, cerita, informasi, dongeng, legenda\n";
        $systemPrompt .= "- Ide must be GLOBAL, not specific story with named characters\n";
        $systemPrompt .= "- JANGAN gunakan 'si' di judul\n";
        $systemPrompt .= "- JANGAN gunakan nama karakter/persona\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},{\"text\":\"...\"},...exactly {$pagesCount} items]}\n";
        $systemPrompt .= "- Theme: {$themeInput}\n";
        $systemPrompt .= "- Each page text MUST be MAXIMUM 40 words\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable.\n";

        $userPrompt = "Buatkan cerita untuk anak tentang tema: {$themeInput}\n\n";
        $userPrompt .= "Jumlah halaman: {$pagesCount}\n";
        $userPrompt .= "Usia: {$minAge}-{$maxAge} tahun\n\n";
        $userPrompt .= "ATURAN PENTING:\n";
        $userPrompt .= "- JANGAN gunakan 'si' di judul (contoh SALAH: 'Raja si Paus', BENAR: 'Paus Sperma di Laut Banda')\n";
        $userPrompt .= "- JANGAN gunakan nama karakter/persona\n";
        $userPrompt .= "- Ide harus GLOBAL, bukan cerita spesifik dengan tokoh\n";
        $userPrompt .= "- Format: Hewan/Objek > Lokasi > fakta, cerita, informasi, dongeng, legenda\n";
        $userPrompt .= "- Gunakan konteks Indonesia\n\n";
        $userPrompt .= "Contoh yang BENAR:\n";
        $userPrompt .= '- "Paus Sperma > Laut Banda > bisa menyelam hingga 3 kilometer"\n';
        $userPrompt .= '- "Pari Manta > Raja Ampat > bisa terbang melompat keluar air"\n\n';
        $userPrompt .= "Output dalam format JSON:\n";
        $userPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},...exactly {$pagesCount} items]}\n\n";
        $userPrompt .= "Hanya output JSON. Semua teks bahasa Indonesia sederhana.";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title']) || empty($result['pages'])) {
                return $this->fallback($theme, $pagesCount);
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
                'desc' => $this->cleanText($result['desc'] ?? ''),
                'moral' => $this->cleanText($result['moral'] ?? ''),
                'pages' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            return $this->fallback($theme, $pagesCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        foreach ($result['pages'] as $index => $page) {
            if ($index === 0) continue;
            $pages[] = ['num' => $index, 'text' => $page['text'] ?? ''];
        }

        return array_merge($this->baseActivityData('storytelling', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages],
        ]);
    }

    public function assetConfig(): array
    {
        return [
            'mode'          => 'grid',
            'default_pages' => 16,
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

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, colorful kid-friendly illustration representing the story theme."];
        foreach ($pages as $i => $p) {
            if ($i === 0) continue;
            $lines[] = "Page {$i}: {$p['text']}";
        }

        $p = "A {$count}-panel comic page storyboard, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\nMoral: {$moral}\n\n";
        $p .= "Each panel is an illustration for the story:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- Cover title is not too big and not too small\n";
        $p .= "- Page 1-{$panel} is story\n";
        $p .= $this->commonRules();

        return $p;
    }

    private function fallback(string $theme, int $pagesCount): array
    {
        $title = 'Kisah tentang ' . ucfirst($theme);
        $pages = [];
        for ($i = 1; $i <= $pagesCount; $i++) {
            $pages[] = ['num' => $i, 'text' => "Halaman {$i} tentang {$theme}"];
        }
        return ['title' => $title, 'desc' => '', 'moral' => '', 'pages' => $pages];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
