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

        if (in_array($type, ['storytelling', 'komik'])) {
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
        $pages = $activity->data['pages'] ?? [];

        if (empty($pages)) {
            $this->warn("  No pages data found.");
            return null;
        }

        $title = $activity->title;
        $desc = $activity->desc ?? '';
        $moral = $activity->moral ?? '';
        $pageCount = count($pages);

        if ($activity->type === 'komik') {
            return $this->buildKomikPrompt($title, $desc, $moral, $pages, $pageCount);
        }

        return $this->buildStorytellingPrompt($title, $desc, $moral, $pages, $pageCount);
    }

    private function buildKomikPrompt(string $title, string $desc, string $moral, array $pages, int $pageCount): string
    {
        $panelDescriptions = [];
        $panelDescriptions[] = "Panel 1 (cover) ukuran panel 1:1: A vibrant and fun scene that captures the essence of the comic story. No text, just characters and setting.";

        foreach ($pages as $i => $page) {
            $panelNum = $i + 2;
            $text = $page['text'] ?? '';
            if ($text) {
                $panelDescriptions[] = "Panel {$panelNum} ukuran panel 1:1 : {$text}";
            }
        }

        $panelsText = implode("\n", $panelDescriptions);
        $moralLine = $moral ? "Moral: {$moral}" : '';

        return <<<PROMPT
Make image high resolution A 16-panel comic page, single image with a 4x4 panel grid.
Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly, expressive characters.

Rules:
- Panel 1 is the cover with main characters and setting, no text
- write speech bubbles with text in any panel refer to character text in each page
- panel have dialog to another characher example Rusa : dialog, means speech bubble in Rusa character
- dont write Rusa : "dialog", but on character have bubble with text "Dialog"
- make panel with split screen with other character
- only use bahasa indonesa with simple refer to text in each page
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No page number
- Clear visual storytelling through character expressions and actions
- Funny expressions, exaggerated emotions
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel
- Bright daylight colors, no dark or night scenes

Comic Title: {$title}
{$panelsText}

{$moralLine}
PROMPT;
    }

    private function buildStorytellingPrompt(string $title, string $desc, string $moral, array $pages, int $pageCount): string
    {
        $panelDescriptions = [];
        $panelDescriptions[] = "Panel 1 (cover) ukuran panel 1:1: Centered on a soft and light vibrant scene that captures the essence of the story." . ($desc ? " {$desc}" : '');

        foreach ($pages as $i => $page) {
            $panelNum = $i + 2;
            $text = $page['text'] ?? '';
            if ($text) {
                $panelDescriptions[] = "Panel {$panelNum} ukuran panel 1:1 : {$text}";
            }
        }

        $panelsText = implode("\n", $panelDescriptions);

        return <<<PROMPT
Make image hight resolution A 16-panel page storyboard, single image with a 4x4 panel grid.
Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.

Rules:
- Panel 1 is the cover with all participant and settings
- Cover no need title just only great image that can represent the story
- No dark or the color, night settings
- No written text in panels
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
    }
}
