<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class StoryGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? '';
        $childName = $input['child'] ?? 'Anak';
        $pagesCount = max(1, min(24, $input['pages'] ?? 16));
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT simple sentences (3-6 words per page). Use basic vocabulary. Focus on colors, animals, family. Each page should be 1 short sentence only. Include 1-2 characters.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short simple sentences (5-10 words per page). Simple story with clear sequence. Each page 1-2 short sentences. Include 2-3 characters with different personalities.",
            default => "Target: older children ages 7-10. Use longer sentences (10-20 words per page). Write a RICH detailed story with MANY scenes and locations. Include at least 3-4 named characters with distinct personalities. Each page describes a DIFFERENT scene/location with detailed action and dialogue. Each page 2-4 sentences with dialogue.",
        };

        $systemPrompt = "You are a children's story writer.\n";
        $systemPrompt .= "CRITICAL: You MUST write EXACTLY {$pagesCount} pages. EXACTLY {$pagesCount} pages in the pages array.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. NEVER use Chinese, Arabic, Japanese, Korean, or any non-Latin characters. No emojis, no special unicode symbols.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "Return ONLY JSON with this structure:\n";
        $systemPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},{\"text\":\"...\"},...up to EXACTLY {$pagesCount} items]}\n";
        $systemPrompt .= "- desc: a short 1-2 sentence summary of what the story is about\n";
        $systemPrompt .= "- moral: the moral lesson(s) of the story\n";
        $systemPrompt .= "- Theme: {$theme}\n";
        if ($childName && $childName !== 'Anak') {
            $systemPrompt .= "- Main character name: {$childName}\n";
        } else {
            $systemPrompt .= "- Create your own cartoon characters (animals, fantasy creatures, or children). Do NOT use the name 'Anak'. Use creative character names like ikan hiu, kucing lucu, kelinci putih, etc.\n";
        }
        $systemPrompt .= "- Number of pages: {$pagesCount}\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge} years old\n";
        $systemPrompt .= "- Each page text MUST be MAXIMUM 40 words. Keep it concise and impactful.\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN words: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable.\n";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, 'Buatkan cerita tentang tema: ' . $theme . ($childName && $childName !== 'Anak' ? ' untuk anak bernama ' . $childName : ''));

            if (!is_array($result) || empty($result['title']) || empty($result['pages'])) {
                return $this->fallback($theme, $childName, $pagesCount);
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
            return $this->fallback($theme, $childName, $pagesCount);
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

    private function fallback(string $theme, string $childName, int $pagesCount): array
    {
        $title = 'Kisah ' . ($childName ?: 'Anak') . ' tentang ' . ucfirst($theme);
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
