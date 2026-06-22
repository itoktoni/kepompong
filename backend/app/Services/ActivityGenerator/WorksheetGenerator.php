<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class WorksheetGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $topic = $input['topic'] ?? $input['theme'] ?? '';
        $desc = $input['desc'] ?? '';
        $informasi = $input['informasi'] ?? $input['moral'] ?? '';
        $subtopic = $input['subtopic'] ?? null;
        $pagesCount = max(1, min(24, $input['pages'] ?? 8));
        $grades = $input['grades'] ?? [1];
        $grade = !empty($grades) ? min($grades) : 1;
        $type = $input['style'] ?? 'practice';
        $variation = $input['variation'] ?? 1;

        $gradeGuide = match (true) {
            $grade <= 1 => "Target: children ages 5-7. Use very simple vocabulary.",
            $grade <= 3 => "Target: children ages 7-9. Use simple vocabulary.",
            default => "Target: children ages 9-10. Use more complex vocabulary.",
        };

        $typeGuide = match (strtolower($type)) {
            'exam' => "Format: exam questions with clear instructions.",
            'activity' => "Format: activity sheets with games and puzzles.",
            default => "Format: practice with examples and exercises.",
        };

        $subjectFocus = $subtopic ? "Topic: {$topic} - Sub-topic: {$subtopic}" : "Topic: {$topic}";
        $topicInput = $topic ?: 'matematika dan bahasa';

        $ideaContext = '';
        $parsed = $this->parseKeterangan($desc);
        $titles = $parsed['titles'];
        $latar = $parsed['latar'];

        $selectedTitle = '';
        if (!empty($titles)) {
            $index = ($variation - 1) % count($titles);
            $selectedTitle = $titles[$index];
        }

        if (!empty($selectedTitle)) {
            $ideaContext .= "WORKSHEET TITLE (you MUST use this exact title):\n\"{$selectedTitle}\"\n\n";
        }
        if (!empty($latar)) {
            $ideaContext .= "SETTING / BACKGROUND:\n{$latar}\n\n";
        }
        if (!empty($informasi)) {
            $ideaContext .= "FACTUAL INFORMATION about \"{$topicInput}\" (use as worksheet content):\n{$informasi}\n";
        }

        $systemPrompt = "You are an educational worksheet designer for Indonesian children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$pagesCount} worksheet pages.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$gradeGuide}\n";
        $systemPrompt .= "{$typeGuide}\n";
        $systemPrompt .= "Format: Worksheet Type > Topic > Brief description\n";
        $systemPrompt .= "- Ideas must be WORKSHEET with clear instructions\n";
        $systemPrompt .= "- DO NOT use 'si' in titles\n";
        $systemPrompt .= "- DO NOT use character names/persona\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"items\":[{\"text\":\"...\"},..exactly {$pagesCount} items]}\n";
        $systemPrompt .= "- {$subjectFocus}\n";
        $systemPrompt .= "- Grade level: {$grade}\n";
        $systemPrompt .= "- Worksheet type: {$type}\n";
        $systemPrompt .= "- Include: matching, filling in, drawing, counting, writing\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious.\n";

        $userContent = 'Create a worksheet for topic: "' . $topicInput . '"';
        if ($subtopic) $userContent .= ' with subtopic: ' . $subtopic;
        $userContent .= '. Type: ' . $type . '. Grade: ' . $grade;

        if (!empty($ideaContext)) {
            $userContent .= "\n\n{$ideaContext}";
        }

        $userContent .= "\n\nCRITICAL: You MUST use the EXACT title provided. Do NOT change it.";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userContent);

            if (!is_array($result) || empty($result['title']) || empty($result['items'])) {
                return $this->fallback($topic, $subtopic, $pagesCount);
            }

            $items = array_slice($result['items'], 0, $pagesCount);
            $renumbered = [];
            foreach ($items as $index => $item) {
                $renumbered[] = [
                    'num' => $index + 1,
                    'text' => $this->cleanText($item['text'] ?? (is_string($item) ? $item : '')),
                ];
            }

            $finalTitle = !empty($selectedTitle) ? $selectedTitle : ($result['title'] ?? $topic);

            return [
                'title' => $this->cleanText($finalTitle),
                'desc' => $this->cleanText($result['desc'] ?? ''),
                'items' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            return $this->fallback($topic, $subtopic, $pagesCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        return array_merge($this->baseActivityData('worksheet', $result, $input), [
            'moral' => null,
            'ages'  => [],
            'data'  => [
                'items'    => $result['items'] ?? [],
                'topic'    => $input['topic'] ?? '',
                'subtopic' => $input['subtopic'] ?? null,
                'type'     => $input['style'] ?? 'practice',
                'grades'   => $input['grades'] ?? [1],
            ],
        ]);
    }

    public function assetConfig(): array
    {
        return [
            'mode'          => 'grid',
            'default_pages' => 8,
            'image_size'    => '2K',
            'style'         => 'Clean educational worksheet design, suitable for children.',
            'extra_rules'   => "- All content should be clear and readable\n- Include clear instructions for each exercise",
        ];
    }

    public function buildPrompt(array $result, array $input): string
    {
        $items = $result['items'] ?? [];
        $count = count($items);
        $title = $result['title'];
        $desc = $result['desc'] ?? '';
        $topic = $input['topic'] ?? '';
        $subtopic = $input['subtopic'] ?? null;
        $grid = $this->gridLabel($count);

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, educational worksheet design for {$topic}."];
        foreach ($items as $i => $item) {
            $lines[] = "Page {$i}: {$item['text']}";
        }

        $p = "A {$count}-panel worksheet sheet, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\n";
        $p .= "Topic: {$topic}" . ($subtopic ? " - {$subtopic}" : "") . "\n\n";
        $p .= "Each panel is a worksheet page:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Clean educational worksheet design, suitable for children.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- All content should be clear and readable\n";
        $p .= $this->commonRules();

        return $p;
    }

    private function fallback(string $topic, ?string $subtopic, int $pagesCount): array
    {
        $items = [];
        for ($i = 1; $i <= $pagesCount; $i++) {
            $items[] = ['num' => $i, 'text' => "Lembar kerja {$i} tentang " . ($subtopic ?: $topic)];
        }
        return ['title' => 'Lembar Kerja ' . ucfirst($topic), 'desc' => '', 'items' => $items];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
