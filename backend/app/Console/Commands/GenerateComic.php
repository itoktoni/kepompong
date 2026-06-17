<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Services\ComicGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateComic extends Command
{
    protected $signature = 'generate:comic
        {theme : Comic theme (e.g. petualangan, persahabatan, kejujuran, kemandirian)}
        {--child= : Child name (auto-generated if empty)}
        {--panels= : Number of panels (default 16)}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}';

    protected $description = 'Generate a children comic with AI and save to database';

    public function handle(ComicGeneratorService $service): int
    {
        $theme = $this->argument('theme');
        $panelsCount = (int) ($this->option('panels') ?: 16);

        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $childName = $this->option('child') ?: null;

        $this->info("Generating comic with AI...");
        $this->line("Theme   : {$theme}");
        $this->line("Child   : " . ($childName ?: '-'));
        $this->line("Panels  : {$panelsCount}");
        $this->line("Ages    : " . implode(',', $ages));
        $this->line("Agama   : " . ($agama ?: '-'));
        $this->newLine();

        $result = $service->generateWithAI($theme, $childName ?: 'Anak', $panelsCount, null, $ages);

        $title = $result['title'];
        $moral = $result['moral'] ?? '';
        $desc = $result['desc'] ?? '';

        $slug = Str::slug($title);

        $pages = [];
        $pagesForPrompt = [];
        $dialogues = [];
        foreach ($result['pages'] as $index => $page) {
            $pagesForPrompt[] = [
                'num' => $index,
                'text' => $page['text'] ?? '',
                'dialogue' => $page['dialogue'] ?? null,
            ];
            if ($index === 0) continue;
            $pages[] = [
                'num' => $index,
                'text' => $page['text'] ?? '',
                'dialogue' => $page['dialogue'] ?? null,
            ];
            if (!empty($page['dialogue'])) {
                $dialogues[] = [
                    'panel' => $index,
                    'text' => $page['dialogue'],
                ];
            }
        }

        $grid = match ($panelsCount) {
            16 => '4x4',
            24 => '6x4',
            20 => '5x4',
            12 => '4x3',
            10 => '5x2',
            9  => '3x3',
            default => '4x4',
        };

        $panelLines = [];
        $panelLines[] = "Panel 1 (cover): Title \"{$title}\" centered, colorful kid-friendly comic illustration representing the story theme.";

        foreach ($pagesForPrompt as $page) {
            if ($page['num'] === 0) continue;

            $line = "Panel {$page['num']}: {$page['text']}";
            if (!empty($page['dialogue'])) {
                $line .= " | Dialogue: \"{$page['dialogue']}\"";
            }
            $panelLines[] = $line;
        }

        $panel = $panelsCount - 1;

        $prompt = "A {$panelsCount}-panel comic page, single image with a {$grid} panel grid.\n\n";
        $prompt .= "Title: {$title}\n";
        $prompt .= "Description: {$desc}\n";
        $prompt .= "Moral: {$moral}\n\n";
        $prompt .= "Each panel is a comic panel illustration:\n\n";
        $prompt .= implode("\n", $panelLines) . "\n\n";
        $prompt .= "Style: Modern comic book style, bright colorful, kid friendly, expressive characters.\n\n";
        $prompt .= "Rules:\n";
        $prompt .= "- Panel 1 is the cover with title text centered\n";
        $prompt .= "- Cover title is not too big and not too small\n";
        $prompt .= "- Panel 2-{$panel} is the comic story\n";
        $prompt .= "- No written text in other panels except cover\n";
        $prompt .= "- 1 Panel must have speech bubbles\n";
        $prompt .= "- 1 Panel can be split into 2 screen\n";
        $prompt .= "- No merged panels, no oversized panels, no rounded corners\n";
        $prompt .= "- No outer border around canvas\n";
        $prompt .= "- No objects crossing panel boundaries\n";
        $prompt .= "- No page number\n";
        $prompt .= "- Funny expressions, clear visual storytelling\n";
        $prompt .= "- Straight vertical and horizontal grid lines only\n";
        $prompt .= "- Pure white divider lines between panels\n";
        $prompt .= "- Every scene fully contained inside its own panel\n";
        $prompt .= "- Reading order left-to-right, top-to-bottom\n";
        $prompt .= "- Perfect square ratio 1:1 for every panel\n";

        $activity = Activity::create([
            'type' => 'komik',
            'title' => $title,
            'slug' => $slug,
            'desc' => $desc ?: 'Komik anak tentang ' . $theme,
            'image' => 'cover.png',
            'moral' => $moral,
            'ages' => $ages,
            'skills' => [],
            'data' => [
                'pages' => $pages,
                'dialogues' => $dialogues,
                'grid' => $grid,
            ],
            'sort_order' => 0,
            'active' => true,
            'views' => 0,
            'status' => 'pending',
            'agama' => $agama ? [$agama] : [],
            'created_by' => 1,
            'prompt' => $prompt,
            'notes' => null,
            'creator' => null,
        ]);

        $this->info("=== {$title} ===");
        $this->newLine();

        if ($desc) {
            $this->line("Desc: {$desc}");
            $this->newLine();
        }

        foreach ($pages as $page) {
            $this->line("  [{$page['num']}] {$page['text']}");
            if (!empty($page['dialogue'])) {
                $this->line("         💬 {$page['dialogue']}");
            }
        }

        $this->newLine();

        if ($moral) {
            $this->comment("Moral: {$moral}");
        }

        $this->newLine();
        $this->info("Saved to database! Activity ID: {$activity->id}");

        return self::SUCCESS;
    }

    private function parseAges(?string $input): array
    {
        if (empty($input)) {
            return range(3, 8);
        }

        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        $age = (int) $input;
        $min = max(1, $age - 1);
        $max = min(10, $age + 3);
        return range($min, $max);
    }
}
