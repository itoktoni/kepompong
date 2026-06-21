<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class ComicGenerator extends BaseGenerator
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
        $panelsCount = max(4, min(25, $input['pages'] ?? 16));

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT simple sentences (3-6 words per panel). Each panel 1 short sentence only.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short simple sentences (5-10 words per panel). Simple story with clear sequence.",
            default => "Target: older children ages 7-10. Use longer sentences (10-20 words per panel). Write a RICH detailed story with MANY scenes.",
        };

        $themeInput = $theme ?: 'petualangan seru';

        $systemPrompt = "You are a children's comic book writer for Indonesia.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$panelsCount} panels.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "Format: Hewan/Objek > Lokasi > cerita dengan konflik dan penyelesaian\n";
        $systemPrompt .= "- Ide must be GLOBAL, not specific story with named characters\n";
        $systemPrompt .= "- JANGAN gunakan 'si' di judul\n";
        $systemPrompt .= "- JANGAN gunakan nama karakter/persona\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\",\"dialogue\":\"...\"},..exactly {$panelsCount} items]}\n";
        $systemPrompt .= "- Theme: {$themeInput}\n";
        $systemPrompt .= "- Each panel MUST have 'text' (MAX 40 words) and 'dialogue' (MAX 10 words)\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious.\n";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, 'Buatkan komik untuk anak tentang tema: ' . $themeInput);

            if (!is_array($result) || empty($result['title']) || empty($result['pages'])) {
                return $this->fallback($theme, $panelsCount);
            }

            $pages = array_slice($result['pages'], 0, $panelsCount);
            $renumbered = [];
            foreach ($pages as $index => $page) {
                $renumbered[] = [
                    'num' => $index + 1,
                    'text' => $this->cleanText($page['text'] ?? (is_string($page) ? $page : '')),
                    'dialogue' => $this->cleanText($page['dialogue'] ?? ''),
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
            return $this->fallback($theme, $panelsCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        $dialogues = [];
        foreach ($result['pages'] as $index => $page) {
            if ($index === 0) continue;
            $pages[] = [
                'num'      => $index,
                'text'     => $page['text'] ?? '',
                'dialogue' => $page['dialogue'] ?? null,
            ];
            if (!empty($page['dialogue'])) {
                $dialogues[] = ['panel' => $index, 'text' => $page['dialogue']];
            }
        }

        return array_merge($this->baseActivityData('komik', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages, 'dialogues' => $dialogues],
        ]);
    }

    public function assetConfig(): array
    {
        return [
            'mode'          => 'grid',
            'default_pages' => 16,
            'image_size'    => '2K',
            'style'         => 'Modern comic book style, bright colorful, kid friendly, expressive characters.',
            'extra_rules'   => "- 1 Panel must have speech bubbles\n- 1 Panel can be split into 2 screen",
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

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, colorful kid-friendly comic illustration."];
        foreach ($pages as $i => $p) {
            if ($i === 0) continue;
            $line = "Panel {$i}: {$p['text']}";
            if (!empty($p['dialogue'])) {
                $line .= " | Dialogue: \"{$p['dialogue']}\"";
            }
            $lines[] = $line;
        }

        $p = "A {$count}-panel comic page, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\nMoral: {$moral}\n\n";
        $p .= "Each panel is a comic panel illustration:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Modern comic book style, bright colorful, kid friendly.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- Panel 2-{$panel} is the comic story\n";
        $p .= "- 1 Panel must have speech bubbles\n";
        $p .= $this->commonRules();

        return $p;
    }

    private function fallback(string $theme, int $panelsCount): array
    {
        $pages = [];
        for ($i = 1; $i <= $panelsCount; $i++) {
            $pages[] = ['num' => $i, 'text' => "Panel {$i} tentang {$theme}", 'dialogue' => ''];
        }
        return ['title' => 'Komik ' . ucfirst($theme), 'desc' => '', 'moral' => '', 'pages' => $pages];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
