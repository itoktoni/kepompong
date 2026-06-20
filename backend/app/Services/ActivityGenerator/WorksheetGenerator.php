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
        $subtopic = $input['subtopic'] ?? null;
        $pagesCount = max(1, min(24, $input['pages'] ?? 8));
        $grades = $input['grades'] ?? [1];
        $grade = !empty($grades) ? min($grades) : 1;
        $type = $input['style'] ?? 'practice';

        $gradeGuide = match (true) {
            $grade <= 1 => "Target: Grade 1 (ages 6-7). Use very simple vocabulary, large fonts, clear images. Focus on basic recognition and matching exercises.",
            $grade <= 3 => "Target: Grade 2-3 (ages 7-9). Use simple vocabulary, moderate complexity. Focus on reading, writing simple words, and basic math.",
            default => "Target: Grade 4+ (ages 9-10). Use complex vocabulary, detailed exercises. Focus on comprehension, problem-solving, and advanced concepts.",
        };

        $typeGuide = match (strtolower($type)) {
            'exam' => "Format: exam-style questions with clear instructions. Include multiple choice, fill-in-the-blank, and short answer formats.",
            'activity' => "Format: hands-on activity sheets with games, puzzles, and creative exercises. Make it fun and engaging.",
            default => "Format: practice worksheets with clear examples and exercises. Mix of guided practice and independent work.",
        };

        $subjectFocus = $subtopic ? "Topic: {$topic} - Subtopic: {$subtopic}" : "Topic: {$topic}";

        $systemPrompt = "You are an educational worksheet designer for Indonesian elementary school children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$pagesCount} worksheet pages.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. NEVER use Chinese, Arabic, Japanese, Korean, or any non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$gradeGuide}\n";
        $systemPrompt .= "{$typeGuide}\n";
        $systemPrompt .= "Return ONLY JSON with this structure:\n";
        $systemPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"items\":[{\"text\":\"...\"},...up to EXACTLY {$pagesCount} items]}\n";
        $systemPrompt .= "- Each item text should be MAXIMUM 35 words describing what the worksheet page contains.\n";
        $systemPrompt .= "- {$subjectFocus}\n";
        $systemPrompt .= "- Grade level: {$grade}\n";
        $systemPrompt .= "- Worksheet type: {$type}\n";
        $systemPrompt .= "- Include varied exercise types: matching, fill-in-blank, drawing, counting, writing, etc.\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious.\n";

        $userContent = 'Buatkan lembar kerja untuk topik: ' . $topic;
        if ($subtopic) $userContent .= ' dengan subtopik: ' . $subtopic;
        $userContent .= '. Tipe: ' . $type . '. Grade: ' . $grade;

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

            return [
                'title' => $this->cleanText($result['title']),
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
