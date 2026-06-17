<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Services\ImageGeneratorService;
use App\Services\ImageSplitterService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;

class GenerateWorksheetImage extends Command
{
    protected $signature = 'generate:worksheet-image
        {id? : Activity ID (omit to process all pending)}
        {--model= : Image model (default: from IMAGE_MODEL env)}
        {--size=2K : Image size (2K, 1024x1024, 512x512)}
        {--pages= : Override page count for splitting (default: from activity data)}
        {--force : Regenerate even if image already exists}';

    protected $description = 'Generate worksheet images using AI and split into pages';

    public function handle(ImageGeneratorService $generator): int
    {
        $id = $this->argument('id');

        if ($id) {
            $activity = Activity::findOrFail($id);
            return $this->processActivity($generator, $activity);
        }

        $activities = Activity::where('type', 'worksheet')
            ->where('status', 'pending')
            ->whereNotNull('prompt')
            ->where('prompt', '!=', '')
            ->orderBy('id')
            ->get();

        if ($activities->isEmpty()) {
            $this->info('No pending worksheet activities with prompts found.');
            return self::SUCCESS;
        }

        $this->info("Found {$activities->count()} pending worksheet activities.");
        $this->newLine();

        $success = 0;
        $failed = 0;

        foreach ($activities as $activity) {
            $result = $this->processActivity($generator, $activity);

            if ($result === self::SUCCESS) {
                $success++;
            } else {
                $failed++;
            }

            $this->newLine();
        }

        $this->info("Done! Success: {$success}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function processActivity(ImageGeneratorService $generator, Activity $activity): int
    {
        $this->info("=== [{$activity->id}] - {$activity->slug} - {$activity->title} ===");

        if (!$activity->prompt) {
            $this->error("  No prompt found. Skipping.");
            return self::FAILURE;
        }

        $pagesCount = $this->option('pages')
            ?: (isset($activity->data['items']) ? count($activity->data['items']) + 1 : 8);

        $grid = ImageSplitterService::getGrid((int) $pagesCount);

        if (!$grid) {
            $this->error("  Unsupported page count: {$pagesCount}. Skipping.");
            return self::FAILURE;
        }

        $folder = "images/worksheets/{$activity->slug}";

        if (!$this->option('force') && \Illuminate\Support\Facades\Storage::disk('public')->exists($folder)) {
            $this->warn("  Image folder already exists. Use --force to regenerate. Skipping.");
            return self::SUCCESS;
        }

        $this->line("  Pages  : {$pagesCount} (grid: {$grid[0]}x{$grid[1]})");
        $this->line("  Model  : " . ($this->option('model') ?: config('services.image.model')));
        $this->line("  Size   : {$this->option('size')}");
        $this->line("  Generating image...");

        $imageUrl = $generator->generate(
            $activity->prompt,
            $this->option('size'),
            $this->option('model'),
        );

        if (!$imageUrl) {
            $this->error("  Failed to generate image.");
            return self::FAILURE;
        }

        $this->line("  Downloading...");

        $tmpPath = $generator->download($imageUrl);

        if (!$tmpPath) {
            $this->error("  Failed to download image.");
            return self::FAILURE;
        }

        $this->line("  Splitting into {$pagesCount} panels...");

        try {
            $file = new UploadedFile(
                $tmpPath,
                'worksheet.png',
                mime_content_type($tmpPath),
                null,
                true
            );

            $result = ImageSplitterService::split($file, $activity->id, (int) $pagesCount, $activity->slug);

            $this->info("  Saved to: {$result['folder']}");
            $this->info("  Files: " . implode(', ', $result['files']));

            @unlink($tmpPath);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("  Split failed: {$e->getMessage()}");
            @unlink($tmpPath);
            return self::FAILURE;
        }
    }
}
