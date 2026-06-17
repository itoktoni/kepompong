<?php

namespace App\Console\Commands;

use App\ActivityType;
use App\Models\Activity;
use App\Services\ActivityImageService;
use App\Services\AiService;
use Illuminate\Console\Command;

class GenerateImage extends Command
{
    protected $signature = 'generate:image
        {id? : Activity ID (omit to process all pending)}
        {--type= : Activity type}
        {--provider= : Image AI provider (from config/ai.php image section)}
        {--model= : Image model (default: from IMAGE_MODEL env)}
        {--size=2K : Image size (2K, 1024x1024, 512x512)}
        {--pages= : Override page count for splitting (default: from activity data)}
        {--force : Regenerate even if image already exists}';

    protected $description = 'Generate activity images using AI and split into pages';

    public function __construct()
    {
        $types = implode(', ', array_column(ActivityType::cases(), 'value'));
        $this->signature = str_replace(
            '{--type= : Activity type}',
            "{--type= : Activity type ({$types})}",
            $this->signature
        );
        parent::__construct();
    }

    public function handle(ActivityImageService $service): int
    {
        $id = $this->argument('id');

        if ($id) {
            $activity = Activity::findOrFail($id);
            return $this->processOne($service, $activity);
        }

        $type = $this->option('type') ?: 'storytelling';

        $activities = Activity::where('status', 'pending')
            ->whereNotNull('prompt')
            ->where('prompt', '!=', '')
            ->where('type', $type)
            ->orderBy('id')
            ->get();

        if ($activities->isEmpty()) {
            $this->info("No pending activities with prompts found for type: {$type}");
            return self::SUCCESS;
        }

        $this->info("Found {$activities->count()} pending {$type} activities.");
        $this->newLine();

        $success = 0;
        $failed = 0;

        foreach ($activities as $activity) {
            $result = $this->processOne($service, $activity);
            $result === self::SUCCESS ? $success++ : $failed++;
            $this->newLine();
        }

        $this->info("Done! Success: {$success}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function processOne(ActivityImageService $service, Activity $activity): int
    {
        $this->info("=== [{$activity->id}] - {$activity->type} - {$activity->slug} ===");

        try {
            $result = $service->process(
                $activity,
                $this->option('size'),
                $this->option('model'),
                $this->option('pages') ? (int) $this->option('pages') : null,
                $this->option('force'),
            );

            if ($result['status'] === 'skipped') {
                $this->warn("  {$result['message']}");
                return self::SUCCESS;
            }

            $this->info("  Folder : {$result['folder']}");
            $this->info("  Pages  : {$result['pages']} (grid: {$result['grid']['cols']}x{$result['grid']['rows']})");
            $this->info("  Files  : " . implode(', ', $result['files']));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("  Failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
