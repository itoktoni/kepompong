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
        $topic = $input['topic'] ?? $input['theme'] ?? '';
        $desc = $input['desc'] ?? '';
        $informasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $child = $input['child'] ?? 'Anak';
        $ages = $input['ages'] ?? [];
        $agama = $input['agama'] ?? null;
        $panelsCount = max(4, min(25, $input['pages'] ?? 9));
        $variation = $input['variation'] ?? 1;

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT simple sentences (3-6 words per panel). Each panel 1 short sentence only.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short simple sentences (5-10 words per panel). Simple story with clear sequence.",
            $maxAge <= 10 => "Target: older children ages 7-10. Use longer sentences (10-20 words per panel). Write a detailed story with many scenes.",
            default => "Target: children ages 7-10. Use longer sentences (10-20 words per panel). Write a detailed story with many scenes.",
        };

        $themeInput = $theme ?: 'petualangan seru';

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
            $ideaContext .= "COMIC TITLE (you MUST use this exact title):\n\"{$selectedTitle}\"\n\n";
        }
        if (!empty($latar)) {
            $ideaContext .= "SETTING / BACKGROUND (use as comic environment):\n{$latar}\n\n";
        }
        if (!empty($informasi)) {
            $ideaContext .= "FACTUAL INFORMATION about \"{$themeInput}\" (use as comic background):\n{$informasi}\n";
        }
        if (!empty($notes)) {
            $ideaContext .= "ADDITIONAL INSTRUCTIONS from user:\n{$notes}\n";
        }
        if (!empty($agama)) {
            $ideaContext .= "Religious context: {$agama}\n";
        }

        $systemPrompt = "You are a children's comic book writer for Indonesia.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$panelsCount} panels.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "TITLE FORMAT: Animal/Object/Child doing Activity\n";
        $systemPrompt .= "STORY MUST EXPLORE MULTIPLE LOCATIONS - do NOT use only one location!\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\",\"dialogue\":\"...\"},..exactly {$panelsCount} items]}\n";
        $systemPrompt .= "- Theme: {$themeInput}\n";
        $systemPrompt .= "- Each panel MUST have 'text' (MAX 40 words) and 'dialogue' (MAX 10 words)\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious.\n";

        $userPrompt = "Create a comic for children about \"{$themeInput}\".\n\n";

        if (!empty($ideaContext)) {
            $userPrompt .= "{$ideaContext}\n";
        }

        $systemPrompt .= "CRITICAL: You MUST use the EXACT title provided. Do NOT change it.\n";

        $userPrompt .= "Number of panels: {$panelsCount}\n";
        $userPrompt .= "Age: {$minAge}-{$maxAge} years old\n\n";
        $userPrompt .= "ABSOLUTE RULES - MUST FOLLOW:\n";
        $userPrompt .= "1. DO NOT use 'si' in titles at all!\n";
        $userPrompt .= "   WRONG: 'Si Paus', 'Pak Si Hiu', 'Dina si Penjelajah'\n";
        $userPrompt .= "   CORRECT: 'Petualangan Paus Sperma', 'Kisah Hiu Paus yang Pemalu'\n";
        $userPrompt .= "2. DO NOT use character names: Human name, please use Paman Kancil, Ibu Kura kura, Adik, Ayah, Bibi etc.\n";
        $userPrompt .= "3. DO NOT use '>' in titles!\n";
        $userPrompt .= "   WRONG: 'Ikan Tongkol > Laut Jawa'\n";
        $userPrompt .= "   CORRECT: 'Kuda Laut Kerdil di Dasar Laut Jawa'\n";
        $userPrompt .= "   CORRECT: 'Petualangan Ikan Tongkol di Laut Jawa'\n";
        $userPrompt .= "4. TITLES MUST BE ATTRACTIVE like children's story book titles!\n";
        $userPrompt .= "   - Titles like: 'Si Kancil yang Cerdik', 'Kelinci dan Kura-kura', 'Petualangan di Hutan'\n";
        $userPrompt .= "   - Can use numbers: '3 Fakta Menarik tentang Hiu'\n";
        $userPrompt .= "   - Can use location: 'Kuda Laut Kerdil di Dasar Laut Jawa'\n";
        $userPrompt .= "   - NOT format: 'Theme > Location > Explanation'\n\n";
        $userPrompt .= "Output in JSON format:\n";
        $userPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\",\"dialogue\":\"...\"},...exactly {$panelsCount} items]}\n\n";
        $userPrompt .= "Only output JSON. All text in simple Indonesian.";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title']) || empty($result['pages'])) {
                return $this->fallback($theme, $desc, $informasi, $panelsCount);
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

            $finalTitle = !empty($selectedTitle) ? $selectedTitle : ($result['title'] ?? $theme);

            return [
                'title' => $this->cleanText($finalTitle),
                'desc' => $this->cleanText($result['desc'] ?? $desc),
                'moral' => $this->cleanText($result['moral'] ?? $informasi),
                'pages' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            return $this->fallback($theme, $desc, $informasi, $panelsCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        $dialogues = [];
        foreach ($result['pages'] as $index => $page) {
            $pages[] = [
                'num'      => $index + 1,
                'text'     => $page['text'] ?? '',
                'dialogue' => $page['dialogue'] ?? null,
            ];
            if (!empty($page['dialogue'])) {
                $dialogues[] = ['panel' => $index + 1, 'text' => $page['dialogue']];
            }
        }

        return array_merge($this->baseActivityData('komik', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages, 'dialogues' => $dialogues],
        ]);
    }

    private function fallback(string $theme, string $desc, string $moral, int $panelsCount): array
    {
        $pages = [];
        for ($i = 1; $i <= $panelsCount; $i++) {
            $pages[] = ['num' => $i, 'text' => "Panel {$i} tentang {$theme}", 'dialogue' => ''];
        }
        return ['title' => 'Komik ' . ucfirst($theme), 'desc' => $desc, 'moral' => $moral, 'pages' => $pages];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
