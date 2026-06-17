<?php

namespace App\Services\ActivityGenerator;

use App\Services\StoryGeneratorService;

class StoryGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        return app(StoryGeneratorService::class)->generateWithAI(
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
}
