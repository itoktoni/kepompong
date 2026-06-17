<?php

namespace App\Console\Commands;

use App\ActivityType;
use App\Console\Concerns\UsesAiProvider;
use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class GenerateActivity extends Command
{
    use UsesAiProvider;

    protected $signature = 'generate:activity
        {type : Activity type}
        {theme : Theme / topic / subject for the activity}
        {--child= : Child name (auto-generated if empty)}
        {--pages= : Number of pages/panels}
        {--ages= : Target ages (e.g. 7 or 3,4,5,6,7,8)}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--subtopic= : Worksheet subtopic (e.g. penjumlahan, huruf)}
        {--grades= : Worksheet grades (e.g. 1 or 1,2,3)}
        {--style= : Coloring style (simple, detailed, mandala) or worksheet type (practice, exam, activity)}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model (run ai:provider <provider> to list)}';

    protected $description = 'Generate activity content with AI (story, comic, coloring, worksheet)';

    public function handle(ActivityGeneratorService $service): int
    {
        $type = $this->argument('type');
        $theme = $this->argument('theme');

        $config = config("activity.types.{$type}");
        if (!$config) {
            $this->error("Unknown type: {$type}");
            $this->line("Available types:");
            foreach (config('activity.types') as $key => $cfg) {
                $this->line("  <comment>{$key}</comment> — {$cfg['label']} ({$cfg['emoji']})");
            }
            return self::FAILURE;
        }

        $input = $this->buildInput($type, $theme, $config);

        $this->info("=== Generating {$config['emoji']} {$config['label']} ===");
        $this->line("Argument : {$theme}");
        foreach ($input as $key => $value) {
            if (in_array($key, ['theme', 'topic'])) continue;
            $display = is_array($value) ? implode(',', $value) : ($value ?: '-');
            $this->line("  {$key} : {$display}");
        }

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;
        $this->newLine();

        $this->line("Calling AI...");
        $result = $service->generateContent($type, $input);

        if (empty($result['title'])) {
            $this->error("AI returned empty result.");
            return self::FAILURE;
        }

        $this->info("Title: {$result['title']}");
        if (!empty($result['desc'])) {
            $this->line("Desc:  {$result['desc']}");
        }
        if (!empty($result['moral'])) {
            $this->comment("Moral: {$result['moral']}");
        }
        $this->newLine();

        $items = $result['pages'] ?? $result['items'] ?? [];
        foreach ($items as $i => $item) {
            $text = $item['text'] ?? '';
            $dialogue = $item['dialogue'] ?? null;
            $this->line("  [{$i}] {$text}");
            if ($dialogue) {
                $this->line("       💬 {$dialogue}");
            }
        }
        $this->newLine();

        $this->line("Saving to database...");
        $activity = $service->createActivity($type, $result, $input);

        $this->info("✓ Saved! Activity ID: {$activity->id}");
        $this->line("  Type  : {$activity->type}");
        $this->line("  Slug  : {$activity->slug}");
        $this->line("  Image : images/{$activity->type}/{$activity->slug}/");

        return self::SUCCESS;
    }

    private function buildInput(string $type, string $theme, array $config): array
    {
        $input = [];

        $argField = $config['argument'];
        $input[$argField] = $theme;
        $input['theme'] = $theme;

        $input['child'] = $this->option('child') ?: null;
        $input['pages'] = (int) ($this->option('pages') ?: $config['default_pages']);
        $input['agama'] = $this->option('agama') ?: null;

        if ($this->option('ages')) {
            $input['ages'] = $this->parseAges($this->option('ages'));
        }

        if ($this->option('grades')) {
            $input['grades'] = $this->parseGrades($this->option('grades'));
        }

        if ($this->option('subtopic')) {
            $input['subtopic'] = $this->option('subtopic');
        }

        if ($this->option('style')) {
            $input['style'] = $this->option('style');
        }

        return $input;
    }

    private function parseAges(string $input): array
    {
        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        $age = (int) $input;
        return range(max(1, $age - 1), min(10, $age + 3));
    }

    private function parseGrades(string $input): array
    {
        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        return [(int) $input];
    }
}
