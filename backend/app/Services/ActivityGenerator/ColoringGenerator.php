<?php

namespace App\Services\ActivityGenerator;

use App\Services\ColoringGeneratorService;

class ColoringGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        return app(ColoringGeneratorService::class)->generateWithAI(
            $input['theme'],
            $input['child'] ?? 'Anak',
            $input['pages'] ?? 12,
            $input['style'] ?? 'simple',
            $input['ages'] ?? [],
        );
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
        $p .= "Style: Black and white line art, suitable for children to color.\n";
        $p .= "Line art style: clean lines, clear boundaries, no shading.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- All panels are black and white line art ONLY\n";
        $p .= "- No grayscale, no shading, no halftone\n";
        $p .= $this->commonRules();
        $p .= "- Designs should be simple enough for children to color\n";
        $p .= "- Include diverse subjects: {$subject}\n";

        return $p;
    }
}
