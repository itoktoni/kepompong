<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

abstract class GenericGenerator extends BaseGenerator
{
    abstract protected function type(): string;
    abstract protected function label(): string;
    abstract protected function contentGuide(): string;
    abstract protected function defaultPages(): int;

    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? $input['topic'] ?? '';
        $desc = $input['desc'] ?? '';
        $informasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $child = $input['child'] ?? 'Anak';
        $ages = $input['ages'] ?? [];
        $agama = $input['agama'] ?? null;
        $pages = $input['pages'] ?? $this->defaultPages();
        $variation = $input['variation'] ?? 1;

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

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
            $ideaContext .= "CONTENT TITLE (you MUST use this exact title):\n\"{$selectedTitle}\"\n\n";
        }
        if (!empty($latar)) {
            $ideaContext .= "SETTING / BACKGROUND (use as content environment):\n{$latar}\n\n";
        }
        if (!empty($informasi)) {
            $ideaContext .= "FACTUAL INFORMATION about \"{$theme}\" (use as content background):\n{$informasi}\n";
        }
        if (!empty($notes)) {
            $ideaContext .= "ADDITIONAL INSTRUCTIONS from user:\n{$notes}\n";
        }
        if (!empty($agama)) {
            $ideaContext .= "Religious context: {$agama}\n";
        }

        $systemPrompt = "You are a content generator for Indonesian children.\n";
        $systemPrompt .= "Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "DO NOT use other languages. DO NOT use difficult/foreign words.\n";
        $systemPrompt .= "Use simple words appropriate for children aged {$minAge}-{$maxAge} years old.\n";
        $systemPrompt .= "CRITICAL: You MUST use the EXACT title provided. Do NOT change it.\n";
        $systemPrompt .= "Output must be in JSON format.";

        $guide = $this->contentGuide();

        $userPrompt = "Create {$this->label()} content for children about \"{$themeInput}\".\n\n";

        if (!empty($ideaContext)) {
            $userPrompt .= "{$ideaContext}\n";
        }

        $userPrompt .= <<<PROMPT
Guide: {$guide}
Number of pages: {$pages}
Age: {$minAge}-{$maxAge} years old

ABSOLUTE RULES - MUST FOLLOW:
1. DO NOT use 'si' in titles at all!
   WRONG: 'Si Paus', 'Pak Si Hiu', 'Dina si Penjelajah'
   CORRECT: 'Petualangan Paus Sperma', 'Kisah Hiu Paus yang Pemalu'
2. DO NOT use character names: Dina, Bono, Luna, Wibi, etc.
3. DO NOT use '>' in titles!
   WRONG: 'Kisah tentang Kuda Laut Kerdil > Dasar Laut Jawa'
   WRONG: 'Ikan Tongkol > Laut Jawa'
   CORRECT: 'Kuda Laut Kerdil di Dasar Laut Jawa'
   CORRECT: 'Petualangan Ikan Tongkol di Laut Jawa'
4. TITLES MUST BE ATTRACTIVE like children's story book titles!
   - Titles like: 'Si Kancil yang Cerdik', 'Kelinci dan Kura-kura', 'Petualangan di Hutan'
   - Can use numbers: '3 Fakta Menarik tentang Hiu'
   - Can use location: 'Kuda Laut Kerdil di Dasar Laut Jawa'
   - NOT format: 'Theme > Location > Explanation'

Output in JSON format:
{
  "title": "Content title",
  "desc": "Short description",
  "moral": "Moral lesson",
  "pages": [
    {"num": 1, "text": "Page 1 content"},
    {"num": 2, "text": "Page 2 content"}
  ]
}

Only output JSON. All text in simple Indonesian.
PROMPT;

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title'])) {
                $fallbackTitle = !empty($selectedTitle) ? $selectedTitle : $theme;
                return [
                    'title' => $fallbackTitle,
                    'desc'  => $desc ?: "Konten {$this->label()} tentang {$theme}",
                    'moral' => $informasi,
                    'pages' => array_map(fn($i) => ['num' => $i, 'text' => "Halaman {$i} tentang {$theme}"], range(1, $pages)),
                ];
            }

            if (!empty($selectedTitle)) {
                $result['title'] = $selectedTitle;
            }

            return $result;
        } catch (\Throwable $e) {
            $fallbackTitle = !empty($selectedTitle) ? $selectedTitle : $theme;
            return [
                'title' => $fallbackTitle,
                'desc'  => $desc ?: "Konten {$this->label()} tentang {$theme}",
                'moral' => $informasi,
                'pages' => array_map(fn($i) => ['num' => $i, 'text' => "Halaman {$i} tentang {$theme}"], range(1, $pages)),
            ];
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        foreach ($result['pages'] ?? [] as $index => $page) {
            $pages[] = ['num' => $page['num'] ?? ($index + 1), 'text' => $page['text'] ?? ''];
        }

        return array_merge($this->baseActivityData($this->type(), $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages],
        ]);
    }

}
