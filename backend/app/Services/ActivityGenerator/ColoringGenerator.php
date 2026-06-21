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
        $pagesCount = max(1, min(24, $input['pages'] ?? 12));
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $ageGuide = match (true) {
            $maxAge <= 4 => "Target: young children ages 3-4. Use VERY SIMPLE outlines, large shapes, thick lines.",
            $maxAge <= 6 => "Target: children ages 5-6. Use simple to medium complexity. Clear outlines, moderate details.",
            default => "Target: children ages 7-10. Use medium to detailed complexity. More intricate designs, smaller details.",
        };

        $themeInput = $subject ?: 'hewan dan alam';

        $systemPrompt = "You are a children's coloring page designer for Indonesia.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$pagesCount} coloring page designs.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "Format: Hewan/Objek > Deskripsi visual > Detail elemen untuk diwarnai\n";
        $systemPrompt .= "- Ide must be BERUPA OBJEK/GAMBAR yang bisa diwarnai\n";
        $systemPrompt .= "- JANGAN gunakan 'si' di judul\n";
        $systemPrompt .= "- JANGAN gunakan nama karakter/persona\n";
        $systemPrompt .= "Return ONLY JSON: {\"title\":\"...\",\"desc\":\"...\",\"items\":[{\"text\":\"...\"},..exactly {$pagesCount} items]}\n";
        $systemPrompt .= "- Theme: {$themeInput}\n";
        $systemPrompt .= "- Each item text MUST be MAXIMUM 30 words describing what to draw\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious.\n";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, 'Buatkan desain halaman mewarnai tentang tema: ' . $themeInput);

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

            return [
                'title' => $this->cleanText($result['title']),
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

    public function assetConfig(): array
    {
        return [
            'mode'          => 'grid',
            'default_pages' => 12,
            'image_size'    => '2K',
            'style'         => 'Black and white line art, suitable for children to color.',
            'extra_rules'   => "- All panels are black and white line art ONLY\n- No grayscale, no shading, no halftone\n- Designs should be simple enough for children to color",
        ];
    }

    public function buildPrompt(array $result, array $input): string
    {
        $items = $result['items'] ?? [];
        $count = count($items);
        $title = $result['title'];
        $desc = $result['desc'] ?? '';
        $subject = $input['theme'];
        $grid = $this->gridLabel($count);

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, a collection of {$subject} coloring pages preview."];
        foreach ($items as $i => $item) {
            $lines[] = "Page {$i}: {$item['text']}";
        }

        $p = "A {$count}-panel coloring page sheet, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\n\n";
        $p .= "Each panel is a coloring page design:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Black and white line art, suitable for children to color.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- All panels are black and white line art ONLY\n";
        $p .= "- No grayscale, no shading, no halftone\n";
        $p .= $this->commonRules();
        $p .= "- Designs should be simple enough for children to color\n";

        return $p;
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
