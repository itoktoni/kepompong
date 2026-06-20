<?php

namespace App\Console\Commands;

use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class GenerateStory extends Command
{
    protected $signature = 'generate:story
        {theme : Story theme}
        {--child= : Child name}
        {--pages= : Number of pages (default 16)}
        {--ages= : Target ages}';

    protected $description = 'Generate a children story with AI (use generate:activity instead)';

    public function handle(ActivityGeneratorService $service): int
    {
        $this->warn('This command is deprecated. Use: php artisan generate:activity storytelling "theme"');
        $this->newLine();

        $theme = $this->argument('theme');
        $input = [
            'theme' => $theme,
            'child' => $this->option('child') ?: 'Anak',
            'pages' => (int) ($this->option('pages') ?: 16),
            'ages'  => $this->parseAges($this->option('ages')),
        ];

        $this->info("Generating story...");
        $result = $service->generateContent('storytelling', $input);
        $activity = $service->createActivity('storytelling', $result, $input);

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
