<?php

namespace App\Services\ActivityGenerator;

use App\Services\ComicGeneratorService;

class ComicGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        return app(ComicGeneratorService::class)->generateWithAI(
            $input['theme'],
            $input['child'] ?? 'Anak',
            $input['pages'] ?? 16,
            null,
            $input['ages'] ?? [],
        );
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        $dialogues = [];
        foreach ($result['pages'] as $index => $page) {
            if ($index === 0) continue;
            $pages[] = [
                'num'      => $index,
                'text'     => $page['text'] ?? '',
                'dialogue' => $page['dialogue'] ?? null,
            ];
            if (!empty($page['dialogue'])) {
                $dialogues[] = ['panel' => $index, 'text' => $page['dialogue']];
            }
        }

        return array_merge($this->baseActivityData('komik', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages, 'dialogues' => $dialogues],
        ]);
    }

    public function assetConfig(): array
    {
        return [
            'mode'          => 'grid',
            'default_pages' => 16,
            'image_size'    => '2K',
            'style'         => 'Modern comic book style, bright colorful, kid friendly, expressive characters.',
            'extra_rules'   => "- 1 Panel must have speech bubbles\n- 1 Panel can be split into 2 screen",
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

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, colorful kid-friendly comic illustration representing the story theme."];
        foreach ($pages as $i => $p) {
            if ($i === 0) continue;
            $line = "Panel {$i}: {$p['text']}";
            if (!empty($p['dialogue'])) {
                $line .= " | Dialogue: \"{$p['dialogue']}\"";
            }
            $lines[] = $line;
        }

        $p = "A {$count}-panel comic page, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\nMoral: {$moral}\n\n";
        $p .= "Each panel is a comic panel illustration:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Modern comic book style, bright colorful, kid friendly, expressive characters.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- Cover title is not too big and not too small\n";
        $p .= "- Panel 2-{$panel} is the comic story\n";
        $p .= "- 1 Panel must have speech bubbles\n";
        $p .= "- 1 Panel can be split into 2 screen\n";
        $p .= $this->commonRules();

        return $p;
    }
}
