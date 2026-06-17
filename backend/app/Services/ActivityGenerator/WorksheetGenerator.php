<?php

namespace App\Services\ActivityGenerator;

use App\Services\WorksheetGeneratorService;

class WorksheetGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        return app(WorksheetGeneratorService::class)->generateWithAI(
            $input['topic'],
            $input['subtopic'] ?? null,
            $input['child'] ?? 'Anak',
            $input['pages'] ?? 8,
            $input['style'] ?? 'practice',
            $input['grades'] ?? [1],
        );
    }

    public function buildActivityData(array $result, array $input): array
    {
        return array_merge($this->baseActivityData('worksheet', $result, $input), [
            'moral' => null,
            'ages'  => [],
            'data'  => [
                'items'    => $result['items'] ?? [],
                'topic'    => $input['topic'],
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
        $topic = $input['topic'];
        $subtopic = $input['subtopic'] ?? null;
        $type = $input['style'] ?? 'practice';
        $grid = $this->gridLabel($count);

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, educational worksheet design for {$topic}."];
        foreach ($items as $i => $item) {
            $lines[] = "Page {$i}: {$item['text']}";
        }

        $p = "A {$count}-panel worksheet sheet, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\n";
        $p .= "Topic: {$topic}" . ($subtopic ? " - {$subtopic}" : "") . "\n";
        $p .= "Type: {$type}\n\n";
        $p .= "Each panel is a worksheet page:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Clean educational worksheet design, suitable for children.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- All content should be clear and readable\n";
        $p .= "- Include clear instructions for each exercise\n";
        $p .= $this->commonRules();

        return $p;
    }
}
