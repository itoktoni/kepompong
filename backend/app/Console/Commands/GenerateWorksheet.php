<?php

namespace App\Console\Commands;

use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class GenerateWorksheet extends Command
{
    protected $signature = 'generate:worksheet
        {topic : Worksheet topic}
        {--subtopic= : Specific subtopic}
        {--pages= : Number of pages (default 8)}
        {--grades= : Target grades}
        {--type= : Worksheet type (practice, exam, activity)}';

    protected $description = 'Generate worksheets (use generate:activity instead)';

    public function handle(ActivityGeneratorService $service): int
    {
        $this->warn('This command is deprecated. Use: php artisan generate:activity worksheet "topic"');
        $this->newLine();

        $input = [
            'topic'    => $this->argument('topic'),
            'subtopic' => $this->option('subtopic'),
            'pages'    => (int) ($this->option('pages') ?: 8),
            'grades'   => $this->parseGrades($this->option('grades')),
            'style'    => $this->option('type') ?: 'practice',
        ];

        $result = $service->generateContent('worksheet', $input);
        $activity = $service->createActivity('worksheet', $result, $input);

        $this->info("Saved! Activity ID: {$activity->id}");
        return self::SUCCESS;
    }

    private function parseGrades(?string $input): array
    {
        if (empty($input)) return [1];
        if (str_contains($input, ',')) return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        return [(int) $input];
    }
}
