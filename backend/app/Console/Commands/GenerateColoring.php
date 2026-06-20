<?php

namespace App\Console\Commands;

use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class GenerateColoring extends Command
{
    protected $signature = 'generate:coloring
        {subject : Coloring subject}
        {--pages= : Number of pages (default 12)}
        {--ages= : Target ages}
        {--style= : Coloring style (simple, detailed, mandala)}';

    protected $description = 'Generate coloring pages (use generate:activity instead)';

    public function handle(ActivityGeneratorService $service): int
    {
        $this->warn('This command is deprecated. Use: php artisan generate:activity coloring "subject"');
        $this->newLine();

        $input = [
            'theme' => $this->argument('subject'),
            'pages' => (int) ($this->option('pages') ?: 12),
            'ages'  => $this->parseAges($this->option('ages')),
            'style' => $this->option('style') ?: 'simple',
        ];

        $result = $service->generateContent('coloring', $input);
        $activity = $service->createActivity('coloring', $result, $input);

        $this->info("Saved! Activity ID: {$activity->id}");
        return self::SUCCESS;
    }

    private function parseAges(?string $input): array
    {
        if (empty($input)) return range(3, 8);
        if (str_contains($input, ',')) return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        $age = (int) $input;
        return range(max(1, $age - 1), min(10, $age + 3));
    }
}
