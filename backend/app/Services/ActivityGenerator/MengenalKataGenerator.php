<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class MengenalKataGenerator extends GenericGenerator
{
    protected function type(): string { return 'mengenal_kata'; }
    protected function label(): string { return 'Mengenal Kata'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Each slide introduces one object with its Indonesian name, English translation, function, description, and a fun fact.';
    }

    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? $input['topic'] ?? '';
        $desc = $input['desc'] ?? '';
        $notes = $input['notes'] ?? '';
        $ages = $input['ages'] ?? [];
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
            $ideaContext .= "SETTING / BACKGROUND:\n{$latar}\n\n";
        }
        if (!empty($notes)) {
            $ideaContext .= "ADDITIONAL INSTRUCTIONS:\n{$notes}\n";
        }

        $systemPrompt = "You are a content generator for Indonesian children.\n";
        $systemPrompt .= "Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "Use simple words appropriate for children aged {$minAge}-{$maxAge} years old.\n";
        $systemPrompt .= "Output must be in JSON format.";

        $userPrompt = "Create Mengenal Kata content for children about \"{$theme}\".\n\n";
        if (!empty($ideaContext)) {
            $userPrompt .= "{$ideaContext}\n";
        }

        $userPrompt .= <<<PROMPT
Number of slides: {$pages}
Age: {$minAge}-{$maxAge} years old

For each slide, provide:
- nama: object name in Indonesian
- english: object name in English
- digunakan_untuk: what it is used for (simple sentence for children)
- fungsi: its function or purpose
- spesifikasi: physical description (shape, color, material)
- fakta: a fun fact starting with "Taukah kamu?"

Output in JSON format:
{
  "title": "Content title",
  "desc": "Short description",
  "tags": ["tag1", "tag2"],
  "slides": [
    {
      "num": 1,
      "nama": "Object name in Indonesian",
      "english": "Object name in English",
      "digunakan_untuk": "What it is used for",
      "fungsi": "Function or purpose",
      "spesifikasi": "Physical description",
      "fakta": "Taukah kamu? Fun fact"
    }
  ]
}

Only output JSON. All text in simple Indonesian except the "english" field.
PROMPT;

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title'])) {
                $fallbackTitle = !empty($selectedTitle) ? $selectedTitle : $theme;
                return [
                    'title' => $fallbackTitle,
                    'desc'  => $desc ?: "Mengenal Kata tentang {$theme}",
                    'tags'  => [],
                    'slides' => array_map(fn($i) => [
                        'num' => $i,
                        'nama' => "Objek {$i}",
                        'english' => "Object {$i}",
                        'digunakan_untuk' => '',
                        'fungsi' => '',
                        'spesifikasi' => '',
                        'fakta' => '',
                    ], range(1, $pages)),
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
                'desc'  => $desc ?: "Mengenal Kata tentang {$theme}",
                'tags'  => [],
                'slides' => array_map(fn($i) => [
                    'num' => $i,
                    'nama' => "Objek {$i}",
                    'english' => "Object {$i}",
                    'digunakan_untuk' => '',
                    'fungsi' => '',
                    'spesifikasi' => '',
                    'fakta' => '',
                ], range(1, $pages)),
            ];
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $slides = [];
        foreach ($result['slides'] ?? $result['pages'] ?? [] as $index => $slide) {
            $slides[] = [
                'num' => $slide['num'] ?? ($index + 1),
                'nama' => $slide['nama'] ?? $slide['text'] ?? '',
                'english' => $slide['english'] ?? '',
                'digunakan_untuk' => $slide['digunakan_untuk'] ?? '',
                'fungsi' => $slide['fungsi'] ?? '',
                'spesifikasi' => $slide['spesifikasi'] ?? '',
                'fakta' => $slide['fakta'] ?? '',
            ];
        }

        return array_merge($this->baseActivityData($this->type(), $result, $input), [
            'moral' => $result['moral'] ?? null,
            'data'  => ['slides' => $slides, 'tags' => $result['tags'] ?? []],
        ]);
    }
}
