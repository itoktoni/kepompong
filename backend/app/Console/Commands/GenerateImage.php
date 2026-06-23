<?php

namespace App\Console\Commands;

use App\ActivityType;
use App\Models\Activity;
use App\Services\ActivityImageService;
use Illuminate\Console\Command;

class GenerateImage extends Command
{
    protected $signature = 'generate:image
        {id? : Activity ID (omit to process all pending)}
        {--type= : Activity type}
        {--model= : Image model (default: from IMAGE_MODEL env)}
        {--size=2K : Image size (2K, 1024x1024, 512x512)}
        {--pages= : Override page count for splitting (default: from activity data)}
        {--force : Regenerate even if image already exists}
        {--prompt-only : Only build prompt, do not generate image}
        {--status= : Filter by status (default: pending)}';

    protected $description = 'Generate activity images using AI and split into pages';

    public function __construct()
    {
        $types = implode(', ', array_column(ActivityType::cases(), 'value'));
        $this->signature = str_replace(
            '{--type= : Activity type}',
            "{--type= : Activity type: {$types}}",
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
        $status = $this->option('status') ?: 'pending';

        $query = Activity::where('type', $type)->where('status', $status);

        if ($type === 'storytelling') {
            $query->where(function ($q) {
                $q->whereNotNull('prompt')->where('prompt', '!=', '')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('data->pages');
                  });
            });
        } else {
            $query->whereNotNull('prompt')->where('prompt', '!=', '');
        }

        $activities = $query->orderBy('id')->get();

        if ($activities->isEmpty()) {
            $this->info("No {$status} activities found for type: {$type}");
            return self::SUCCESS;
        }

        $this->info("Found {$activities->count()} {$status} {$type} activities.");
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

        $prompt = $this->buildPrompt($activity);

        if ($prompt) {
            $activity->prompt = $prompt;
            $activity->save();
            $this->info("  Prompt saved (" . strlen($prompt) . " chars)");
        }

        if ($this->option('prompt-only')) {
            $this->info("  Prompt-only mode, skipping image generation.");
            return self::SUCCESS;
        }

        if (empty($activity->prompt)) {
            $this->warn("  No prompt available, skipping.");
            return self::FAILURE;
        }

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

    private function buildPrompt(Activity $activity): ?string
    {
        if ($activity->type !== 'storytelling') {
            return null;
        }

        $pages = $activity->data['pages'] ?? [];

        if (empty($pages)) {
            $this->warn("  No pages data found.");
            return null;
        }

        $title = $activity->title;
        $desc = $activity->desc ?? '';
        $moral = $activity->moral ?? '';

        $panelDescriptions = [];
        $panelDescriptions[] = "Panel 1 (cover) ukuran panel 1:1: Title text \"{$title}\" centered on a vibrant scene that captures the essence of the story." . ($desc ? " {$desc}" : '');

        foreach ($pages as $i => $page) {
            $panelNum = $i + 2;
            $text = $page['text'] ?? '';
            if ($text) {
                $panelDescriptions[] = "Panel {$panelNum} ukuran panel 1:1 : {$text}";
            }
        }

        $panelsText = implode("\n", $panelDescriptions);

        $prompt = <<<PROMPT
A 16-panel comic page storyboard, single image with a 4x4 panel grid.
Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.

Rules:
- Panel 1 is the cover with title text centered
- Cover title is not too big and not too small
- No written text in other panels except cover
- No speech bubbles allowed
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No page number
- Funny expressions, clear visual storytelling
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel

Story: {$title}
{$panelsText}

Moral lesson: {$moral}
PROMPT;

        return $prompt;
    }
}
