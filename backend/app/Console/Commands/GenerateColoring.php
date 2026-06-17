<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Services\ColoringGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateColoring extends Command
{
    protected $signature = 'generate:coloring
        {subject : Coloring subject (e.g. hewan, buah, kendaraan, dinosaur, princess, superhero)}
        {--child= : Child name (auto-generated if empty)}
        {--pages= : Number of pages (default 12)}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--style= : Coloring style (simple, detailed, mandala)}';

    protected $description = 'Generate coloring pages with AI and save to database';

    public function handle(ColoringGeneratorService $service): int
    {
        $subject = $this->argument('subject');
        $pagesCount = (int) ($this->option('pages') ?: 12);
        $style = $this->option('style') ?: 'simple';

        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $childName = $this->option('child') ?: null;

        $this->info("Generating coloring pages with AI...");
        $this->line("Subject : {$subject}");
        $this->line("Style   : {$style}");
        $this->line("Child   : " . ($childName ?: '-'));
        $this->line("Pages   : {$pagesCount}");
        $this->line("Ages    : " . implode(',', $ages));
        $this->line("Agama   : " . ($agama ?: '-'));
        $this->newLine();

        $result = $service->generateWithAI($subject, $childName ?: 'Anak', $pagesCount, $style, $ages);

        $title = $result['title'];
        $desc = $result['desc'] ?? '';
        $items = $result['items'] ?? [];

        $slug = Str::slug($title);

        $grid = match ($pagesCount) {
            4  => '2x2',
            6  => '3x2',
            8  => '4x2',
            9  => '3x3',
            10 => '5x2',
            12 => '4x3',
            16 => '4x4',
            20 => '5x4',
            24 => '6x4',
            default => '4x3',
        };

        $panelLines = [];
        $panelLines[] = "Panel 1 (cover): Title \"{$title}\" centered, a collection of {$subject} coloring pages preview.";

        foreach ($items as $index => $item) {
            $panelLines[] = "Page {$index}: {$item['text']}";
        }

        $prompt = "A {$pagesCount}-panel coloring page sheet, single image with a {$grid} panel grid.\n\n";
        $prompt .= "Title: {$title}\n";
        $prompt .= "Description: {$desc}\n\n";
        $prompt .= "Each panel is a coloring page design:\n\n";
        $prompt .= implode("\n", $panelLines) . "\n\n";
        $prompt .= "Style: Black and white line art, suitable for children to color.\n";
        $prompt .= "Line art style: clean lines, clear boundaries, no shading.\n\n";
        $prompt .= "Rules:\n";
        $prompt .= "- Panel 1 is the cover with title text centered\n";
        $prompt .= "- All panels are black and white line art ONLY\n";
        $prompt .= "- No grayscale, no shading, no halftone\n";
        $prompt .= "- No written text in other panels except cover\n";
        $prompt .= "- No speech bubbles allowed\n";
        $prompt .= "- No merged panels, no oversized panels, no rounded corners\n";
        $prompt .= "- No outer border around canvas\n";
        $prompt .= "- No objects crossing panel boundaries\n";
        $prompt .= "- No Page number\n";
        $prompt .= "- Straight vertical and horizontal grid lines only\n";
        $prompt .= "- Pure white divider lines between panels\n";
        $prompt .= "- Every design fully contained inside its own panel\n";
        $prompt .= "- Reading order left-to-right, top-to-bottom\n";
        $prompt .= "- Perfect square ratio 1:1 for every panel\n";
        $prompt .= "- Designs should be simple enough for children to color\n";
        $prompt .= "- Include diverse subjects: {$subject}\n";

        $activity = Activity::create([
            'type' => 'coloring',
            'title' => $title,
            'slug' => $slug,
            'desc' => $desc ?: 'Halaman mewarnai tentang ' . $subject,
            'image' => 'cover.png',
            'moral' => null,
            'ages' => $ages,
            'skills' => [],
            'data' => ['items' => $items, 'style' => $style],
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

        foreach ($items as $item) {
            $this->line("  [{$item['num']}] {$item['text']}");
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
