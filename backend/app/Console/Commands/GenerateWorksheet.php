<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Services\WorksheetGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateWorksheet extends Command
{
    protected $signature = 'generate:worksheet
        {topic : Worksheet topic (e.g. matematika, bahasa, sains,	logika)}
        {--subtopic= : Specific subtopic (e.g. penjumlahan, pengurangan, huruf, angka)}
        {--child= : Child name (auto-generated if empty)}
        {--pages= : Number of pages (default 8)}
        {--grades= : Target grades, e.g. 1 means grade 1 or comma-separated 1,2,3}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--type= : Worksheet type (practice, exam, activity)}';

    protected $description = 'Generate worksheets with AI and save to database';

    public function handle(WorksheetGeneratorService $service): int
    {
        $topic = $this->argument('topic');
        $subtopic = $this->option('subtopic');
        $pagesCount = (int) ($this->option('pages') ?: 8);
        $type = $this->option('type') ?: 'practice';

        $grades = $this->parseGrades($this->option('grades'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $childName = $this->option('child') ?: null;

        $this->info("Generating worksheet with AI...");
        $this->line("Topic    : {$topic}");
        $this->line("Subtopic : " . ($subtopic ?: '-'));
        $this->line("Type     : {$type}");
        $this->line("Child    : " . ($childName ?: '-'));
        $this->line("Pages    : {$pagesCount}");
        $this->line("Grades   : " . implode(',', $grades));
        $this->line("Agama    : " . ($agama ?: '-'));
        $this->newLine();

        $result = $service->generateWithAI($topic, $subtopic, $childName ?: 'Anak', $pagesCount, $type, $grades);

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
            default => '4x2',
        };

        $panelLines = [];
        $panelLines[] = "Panel 1 (cover): Title \"{$title}\" centered, educational worksheet design for {$topic}.";

        foreach ($items as $index => $item) {
            $panelLines[] = "Page {$index}: {$item['text']}";
        }

        $prompt = "A {$pagesCount}-panel worksheet sheet, single image with a {$grid} panel grid.\n\n";
        $prompt .= "Title: {$title}\n";
        $prompt .= "Description: {$desc}\n";
        $prompt .= "Topic: {$topic}" . ($subtopic ? " - {$subtopic}" : "") . "\n";
        $prompt .= "Type: {$type}\n\n";
        $prompt .= "Each panel is a worksheet page:\n\n";
        $prompt .= implode("\n", $panelLines) . "\n\n";
        $prompt .= "Style: Clean educational worksheet design, suitable for children.\n\n";
        $prompt .= "Rules:\n";
        $prompt .= "- Panel 1 is the cover with title text centered\n";
        $prompt .= "- All content should be clear and readable\n";
        $prompt .= "- No written text in other panels except cover\n";
        $prompt .= "- No speech bubbles allowed\n";
        $prompt .= "- No merged panels, no oversized panels, no rounded corners\n";
        $prompt .= "- No outer border around canvas\n";
        $prompt .= "- No objects crossing panel boundaries\n";
        $prompt .= "- No Page number\n";
        $prompt .= "- Straight vertical and horizontal grid lines only\n";
        $prompt .= "- Pure white divider lines between panels\n";
        $prompt .= "- Every worksheet fully contained inside its own panel\n";
        $prompt .= "- Reading order left-to-right, top-to-bottom\n";
        $prompt .= "- Perfect square ratio 1:1 for every panel\n";
        $prompt .= "- Include clear instructions for each exercise\n";

        $activity = Activity::create([
            'type' => 'worksheet',
            'title' => $title,
            'slug' => $slug,
            'desc' => $desc ?: 'Lembar kerja tentang ' . $topic,
            'image' => 'cover.png',
            'moral' => null,
            'ages' => [],
            'skills' => [],
            'data' => [
                'items' => $items,
                'topic' => $topic,
                'subtopic' => $subtopic,
                'type' => $type,
                'grades' => $grades,
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

        foreach ($items as $item) {
            $this->line("  [{$item['num']}] {$item['text']}");
        }

        $this->newLine();
        $this->info("Saved to database! Activity ID: {$activity->id}");

        return self::SUCCESS;
    }

    private function parseGrades(?string $input): array
    {
        if (empty($input)) {
            return [1];
        }

        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        $grade = (int) $input;
        return [$grade];
    }
}
