<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class ColoringGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $subject = $input['theme'] ?? '';
        $desc = $input['desc'] ?? '';
        $informasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $pagesCount = max(1, min(24, $input['pages'] ?? 12));
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;
        $variation = $input['variation'] ?? 1;

        $ageGuide = match (true) {
            $maxAge <= 4 => "Target: young children ages 3-4. Use VERY SIMPLE outlines, large shapes, thick lines.",
            $maxAge <= 6 => "Target: children ages 5-6. Use simple to medium complexity. Clear outlines, moderate details.",
            $maxAge <= 10 => "Target: children ages 7-10. Use medium to detailed complexity. More intricate designs, smaller details.",
            default => "Target: children ages 7-10. Use medium to detailed complexity. More intricate designs, smaller details.",
        };

        $themeInput = $subject ?: 'hewan dan alam';

        $parsed = $this->parseKeterangan($desc);
        $titles = $parsed['titles'];
        $latar = $parsed['latar'];

        $selectedTitle = '';
        if (!empty($titles)) {
            $index = ($variation - 1) % count($titles);
            $selectedTitle = $titles[$index];
        }

        $ideaContext = '';
        if (!empty($selectedTitle)) {
            $ideaContext .= "COLORING PAGE TITLE (you MUST use this exact title):\n\"{$selectedTitle}\"\n\n";
        }
        if (!empty($latar)) {
            $ideaContext .= "SETTING / BACKGROUND:\n{$latar}\n\n";
        }
        if (!empty($informasi)) {
            $ideaContext .= "FACTUAL INFORMATION about \"{$themeInput}\" (use as coloring subject reference):\n{$informasi}\n";
        }
        if (!empty($notes)) {
            $ideaContext .= "ADDITIONAL INSTRUCTIONS from user:\n{$notes}\n";
        }

        $systemPrompt = "You are a children's coloring page designer for Indonesia.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$pagesCount} coloring page designs.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "- Ideas must be DRAWABLE OBJECTS/PICTURES that can be colored\n";
        $systemPrompt .= "- DO NOT use 'si' in titles\n";
        $systemPrompt .= "- DO NOT use character names/persona\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"items\":[{\"text\":\"...\"},..exactly {$pagesCount} items]}\n";
        $systemPrompt .= "- Subject: {$themeInput}\n";
        $systemPrompt .= "- Each item text MUST be MAXIMUM 30 words describing what to draw\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious.\n";

        $userContent = 'Buatkan desain halaman mewarnai tentang: "' . $themeInput . '"';
        if (!empty($ideaContext)) {
            $userContent .= "\n\n{$ideaContext}";
        }
        $userContent .= "\n\nCRITICAL: You MUST use the EXACT title provided. Do NOT change it.";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userContent);

            if (!is_array($result) || empty($result['title']) || empty($result['items'])) {
                return $this->fallback($subject, $pagesCount);
            }

            $items = array_slice($result['items'], 0, $pagesCount);
            $renumbered = [];
            foreach ($items as $index => $item) {
                $renumbered[] = [
                    'num' => $index + 1,
                    'text' => $this->cleanText($item['text'] ?? (is_string($item) ? $item : '')),
                ];
            }

            $finalTitle = !empty($selectedTitle) ? $selectedTitle : ($result['title'] ?? $subject);

            return [
                'title' => $this->cleanText($finalTitle),
                'desc' => $this->cleanText($result['desc'] ?? ''),
                'items' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            return $this->fallback($subject, $pagesCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        return array_merge($this->baseActivityData('coloring', $result, $input), [
            'moral' => null,
            'data'  => ['items' => $result['items'] ?? [], 'style' => $input['style'] ?? 'simple'],
        ]);
    }

    private function fallback(string $subject, int $pagesCount): array
    {
        $items = [];
        for ($i = 1; $i <= $pagesCount; $i++) {
            $items[] = ['num' => $i, 'text' => "Gambar {$i} tentang {$subject} untuk diwarnai."];
        }
        return ['title' => 'Mewarnai ' . ucfirst($subject), 'desc' => '', 'items' => $items];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
