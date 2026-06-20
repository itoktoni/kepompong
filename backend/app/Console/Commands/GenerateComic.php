<?php

namespace App\Console\Commands;

use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class GenerateComic extends Command
{
    protected $signature = 'generate:comic
        {theme : Comic theme}
        {--child= : Child name}
        {--panels= : Number of panels (default 16)}
        {--ages= : Target ages}';

    protected $description = 'Generate a children comic (use generate:activity instead)';

    public function handle(ActivityGeneratorService $service): int
    {
        $this->warn('This command is deprecated. Use: php artisan generate:activity komik "theme"');
        $this->newLine();

        $input = [
            'theme' => $this->argument('theme'),
            'child' => $this->option('child') ?: 'Anak',
            'pages' => (int) ($this->option('panels') ?: 16),
            'ages'  => $this->parseAges($this->option('ages')),
        ];

        $result = $service->generateContent('komik', $input);
        $activity = $service->createActivity('komik', $result, $input);

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
