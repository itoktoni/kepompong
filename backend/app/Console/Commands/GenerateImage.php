<?php

namespace App\Console\Commands;

use App\ActivityType;
use App\Models\Activity;
use App\Services\ActivityGeneratorService;
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

        if (in_array($type, ['storytelling', 'komik', 'bermain_peran', 'outdoor', 'mengenal_kata'])) {
            $query->where(function ($q) {
                $q->whereNotNull('prompt')->where('prompt', '!=', '')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('data->pages');
                  })
                  ->orWhere(function ($q3) {
                      $q3->whereNotNull('data->slides');
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

    public function buildPrompt(Activity $activity): ?string
    {
        $data = $activity->data ?? [];
        $pages = $data['pages'] ?? $data['slides'] ?? [];

        if (empty($pages) && in_array($activity->type, ['storytelling', 'komik']) && !$this->option('prompt-only')) {
            $pages = $this->generatePagesContent($activity);
            if (!empty($pages)) {
                $data = $activity->fresh()->data ?? [];
                $pages = $data['pages'] ?? $pages;
            }
        }

        if (empty($pages)) {
            $this->warn("  No pages data found. Data keys: " . implode(', ', array_keys($data)));
            return null;
        }

        $title = $activity->title;
        $desc = $activity->desc ?? '';
        $moral = $activity->moral ?? '';
        $pageCount = count($pages);

        if ($activity->type === 'komik') {
            return $this->buildKomikPrompt($title, $desc, $moral, $pages, $pageCount);
        }

        if ($activity->type === 'bermain_peran') {
            return $this->buildRoleplayPrompt($title, $desc, $moral, $data, $pageCount);
        }

        if ($activity->type === 'outdoor') {
            return $this->buildOutdoorPrompt($title, $desc, $moral, $pages, $pageCount);
        }

        if ($activity->type === 'mengenal_kata') {
            return $this->buildMengenalKataPrompt($title, $desc, $data, $pageCount);
        }

        return $this->buildStorytellingPrompt($title, $desc, $moral, $pages, $pageCount);
    }

    private function buildKomikPrompt(string $title, string $desc, string $moral, array $pages, int $pageCount): string
    {
        $totalPanels = $pageCount + 1;
        $gridSize = (int) ceil(sqrt($totalPanels));

        $panelDescriptions = [];
        $panelDescriptions[] = "Panel 1 (cover) 1:1: A vibrant scene capturing the comic story essence. Show main characters and setting. No text.";

        foreach ($pages as $i => $page) {
            $panelNum = $i + 2;
            $text = $page['text'] ?? '';
            if ($text) {
                $panelDescriptions[] = "Panel {$panelNum} 1:1: {$text}";
            }
        }

        $panelsText = implode("\n", $panelDescriptions);
        $moralLine = $moral ? "Moral: {$moral}" : '';

        return <<<PROMPT
Make a high resolution {$totalPanels}-panel comic page, single image with a {$gridSize}x{$gridSize} panel grid.
Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly, expressive characters.

Rules:
- Panel 1 is the cover with main characters and setting, no text
- For dialogue lines like "Name : text", draw that character with a speech bubble containing "text" in Indonesian
- Every panel in comic must have text !, if no dialog please add like the setting or background, draw top center title or bottom center title containing "text" in Indonesian
- Do NOT write "Name : text" literally — show it as a speech bubble on the character
- When multiple characters speak in one panel, use split screen or show both characters with their own bubbles
- Use simple Bahasa Indonesia for any visible text in speech bubbles
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No page numbers
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

    private function buildRoleplayPrompt(string $title, string $desc, string $moral, array $data, int $pageCount): string
    {
        $roles = $data['roles'] ?? [];
        $pages = $data['pages'] ?? [];
        $totalPanels = $pageCount + 1;
        $gridCols = 3;
        $gridRows = 3;

        $roleNames = array_map(fn($r) => $r['name'] ?? '', $roles);
        $roleList = implode(', ', $roleNames);

        $panelDescriptions = [];
        $panelDescriptions[] = "Panel 1 (cover) 1:1: A vibrant scene showing the roleplay characters: {$roleList}. {$desc} No text, no speech bubbles.";

        foreach ($pages as $i => $page) {
            $panelNum = $i + 2;
            $narrator = $page['narrator'] ?? '';
            $dialog = $page['dialog'] ?? [];

            $dialogTexts = [];
            foreach ($dialog as $d) {
                $role = $d['role'] ?? '';
                $text = $d['text'] ?? '';
                if ($role && $text) {
                    $dialogTexts[] = "{$role}: {$text}";
                }
            }

            $scene = $narrator;
            if (!empty($dialogTexts)) {
                $scene .= ' Dialog: ' . implode(' | ', $dialogTexts);
            }

            if ($scene) {
                $panelDescriptions[] = "Panel {$panelNum} 1:1: {$scene}";
            }
        }

        $panelsText = implode("\n", $panelDescriptions);
        $moralLine = $moral ? "Moral: {$moral}" : '';

        return <<<PROMPT
Make a high resolution {$totalPanels}-panel roleplay storyboard page, single image with a {$gridCols}x{$gridRows} panel grid.
Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly, expressive characters.

Characters: {$roleList}

Rules:
- Panel 1 is the cover showing all characters in the scene, no text
- For dialogue lines like "Name : text", draw that character with a speech bubble containing "text" in Indonesian
- Do NOT write "Name : text" literally — show it as a speech bubble on the character
- When multiple characters speak in one panel, use split screen or show both characters with their own bubbles
- Use simple Bahasa Indonesia for any visible text in speech bubbles
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No page numbers
- Clear visual storytelling through character expressions and actions
- Funny expressions, exaggerated emotions
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel
- Bright daylight colors, no dark or night scenes
- Each character should be visually distinct and consistent across all panels

Story: {$title}
{$panelsText}

{$moralLine}
PROMPT;
    }

    private function buildOutdoorPrompt(string $title, string $desc, string $moral, array $pages, int $pageCount): string
    {
        $totalPanels = $pageCount + 1;
        $gridSize = (int) ceil(sqrt($totalPanels));

        $panelDescriptions = [];
        $panelDescriptions[] = "Panel 1 (cover) ukuran panel 1:1: A vibrant outdoor nature scene that captures the essence of the exploration. {$desc} No text, no speech bubbles.";

        foreach ($pages as $i => $page) {
            $panelNum = $i + 2;
            $text = $page['text'] ?? $page['desc'] ?? $page['step'] ?? '';
            if ($text) {
                $panelDescriptions[] = "Panel {$panelNum} ukuran panel 1:1: {$text}";
            }
        }

        $panelsText = implode("\n", $panelDescriptions);

        return <<<PROMPT
Make image high resolution A {$totalPanels}-panel page storyboard, single image with a {$gridSize}x{$gridSize} panel grid.
Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly, nature exploration theme.

Rules:
- Panel 1 is the cover showing the outdoor exploration scene
- Cover no need title just only great image that can represent the exploration
- No dark or night settings
- No written text in panels
- No speech bubbles allowed
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No page number
- Show nature elements clearly: plants, animals, sky, water, insects
- Funny expressions, clear visual storytelling
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel
- Bright daylight colors, green nature tones

Exploration: {$title}
{$panelsText}

Moral lesson: {$moral}
PROMPT;
    }

    private function buildMengenalKataPrompt(string $title, string $desc, array $data, int $pageCount): string
    {
        $slides = $data['slides'] ?? [];
        $totalPanels = $pageCount + 1;
        $gridSize = (int) ceil(sqrt($totalPanels));

        $panelDescriptions = [];
        $panelDescriptions[] = "Panel 1 (cover) 1:1: A vibrant, colorful flat illustration of various fruits arranged nicely. Kid friendly, clean white background. No text.";

        foreach ($slides as $i => $slide) {
            $panelNum = $i + 2;
            $nama = $slide['nama'] ?? '';
            $english = $slide['english'] ?? '';
            $spesifikasi = $slide['spesifikasi'] ?? '';
            if ($nama) {
                $panelDescriptions[] = "Panel {$panelNum} 1:1: A single whole {$nama} ({$english}) on a clean white background. {$spesifikasi} Kid friendly flat illustration, vibrant colors, centered, no text.";
            }
        }

        $panelsText = implode("\n", $panelDescriptions);

        return <<<PROMPT
Make a high resolution {$totalPanels}-panel page, single image with a {$gridSize}x{$gridSize} panel grid.
Style: Modern flat illustration, bright vibrant colors, kid friendly, clean white background per panel.

Rules:
- Panel 1 is the cover showing a variety of fruits nicely arranged
- Each other panel shows ONE fruit only, centered, whole fruit visible
- Clean white background for every panel
- No text, no labels, no watermarks
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No page numbers
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel
- Bright daylight colors, vibrant and saturated
- Each fruit should look realistic but kid-friendly

Theme: {$title}
{$panelsText}
PROMPT;
    }

    private function generatePagesContent(Activity $activity): array
    {
        $this->info("  Generating pages content via AI...");

        try {
            $generatorService = app(ActivityGeneratorService::class);
            $input = [
                'theme' => $activity->title,
                'desc'  => $activity->desc ?? '',
                'moral' => $activity->moral ?? '',
                'ages'  => $activity->ages ?? [],
                'agama' => $activity->agama ?? [],
                'pages' => 9,
            ];

            $result = $generatorService->generateContent($activity->type, $input);
            $activityData = $generatorService->buildActivityData($activity->type, $result, $input);

            if (!empty($activityData['data'])) {
                $activity->data = $activityData['data'];
                if (!empty($activityData['moral']) && empty($activity->moral)) {
                    $activity->moral = $activityData['moral'];
                }
                $activity->save();
                $this->info("  Pages generated and saved (" . count($activityData['data']['pages'] ?? []) . " pages)");
                return $activityData['data']['pages'] ?? [];
            }
        } catch (\Throwable $e) {
            $this->warn("  Content generation failed: {$e->getMessage()}");
        }

        return [];
    }

    private function buildStorytellingPrompt(string $title, string $desc, string $moral, array $pages, int $pageCount): string
    {
        $totalPanels = $pageCount + 1;
        $gridSize = (int) ceil(sqrt($totalPanels));

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
Make image high resolution A {$totalPanels}-panel page storyboard, single image with a {$gridSize}x{$gridSize} panel grid.
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
