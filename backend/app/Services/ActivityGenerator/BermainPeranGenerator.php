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
        $ideaDesc = $input['desc'] ?? '';
        $ideaInformasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;
        $pagesCount = max(1, min(25, $input['pages'] ?? 9));
        $variation = $input['variation'] ?? 1;

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT sentences (3-6 words per page). 1 sentence per page.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short sentences (5-10 words per page). Simple scenario with clear sequence.",
            $maxAge <= 10 => "Target: older children ages 7-10. Use longer sentences (10-20 words per page). More detailed scenario with many scenes.",
            default => "Target: children ages 7-10. Use longer sentences (10-20 words per page). More detailed scenario with many scenes.",
        };

        $themeInput = $theme ?: 'bermain peran seru';

        $parsed = $this->parseKeterangan($ideaDesc);
        $titles = $parsed['titles'];
        $latar = $parsed['latar'];

        $selectedTitle = '';
        if (!empty($titles)) {
            $index = ($variation - 1) % count($titles);
            $selectedTitle = $titles[$index];
        }

        $systemPrompt = "You are a role-play scenario writer for Indonesian children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$pagesCount} pages.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "- Scenario must be EASY for children to play\n";
        $systemPrompt .= "- Use familiar professions/situations: doctor, cook, teacher, police, astronaut, etc\n";
        $systemPrompt .= "- DO NOT use 'si' in titles\n";
        $systemPrompt .= "- DO NOT use character names/persona\n";
        $systemPrompt .= "- Ideas must be GLOBAL, a role-play scenario that can be played\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},..exactly {$pagesCount} items]}\n";
        $systemPrompt .= "- Subject: {$themeInput}\n";
        $systemPrompt .= "- Each page contains ONE scene or dialogue (MAX 40 words)\n";
        $systemPrompt .= "CRITICAL: You MUST use the EXACT title provided. Do NOT change it.\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable.\n";

        $userPrompt = "Create a role-play scenario for children about \"{$themeInput}\".\n\n";
        if (!empty($selectedTitle)) {
            $userPrompt .= "SCENARIO TITLE (you MUST use this exact title):\n\"{$selectedTitle}\"\n\n";
        }
        if (!empty($latar)) {
            $userPrompt .= "SETTING / BACKGROUND (use as scenario environment):\n{$latar}\n\n";
        }
        if ($ideaInformasi) {
            $userPrompt .= "FACTUAL INFORMATION about \"{$themeInput}\" (use as scenario background):\n{$ideaInformasi}\n\n";
        }
        if (!empty($notes)) {
            $userPrompt .= "ADDITIONAL INSTRUCTIONS from user:\n{$notes}\n";
        }
        $userPrompt .= "Number of pages: {$pagesCount}\n";
        $userPrompt .= "Age: {$minAge}-{$maxAge} years old\n\n";
        $userPrompt .= "IMPORTANT RULES:\n";
        $userPrompt .= "- DO NOT use 'si' in titles\n";
        $userPrompt .= "- DO NOT use character names/persona\n";
        $userPrompt .= "- Each page = one role-play scene or dialogue\n";
        $userPrompt .= "- Use Indonesian context\n\n";
        $userPrompt .= "Output in JSON format:\n";
        $userPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},..exactly {$pagesCount} items]}\n\n";
        $userPrompt .= "Only output JSON. All text in simple Indonesian.";

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

            $finalTitle = !empty($selectedTitle) ? $selectedTitle : ($result['title'] ?? $theme);

            return [
                'title' => $this->cleanText($finalTitle),
                'desc' => $this->cleanText($result['desc'] ?? $ideaDesc),
                'moral' => $this->cleanText($result['moral'] ?? $ideaInformasi),
                'pages' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            return $this->fallback($theme, $ideaDesc, $ideaInformasi, $pagesCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        foreach ($result['pages'] as $index => $page) {
            $pages[] = ['num' => $index + 1, 'text' => $page['text'] ?? ''];
        }

        return array_merge($this->baseActivityData('bermain_peran', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages],
        ]);
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
