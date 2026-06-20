<?php

namespace App\Console\Commands;

use App\Console\Concerns\UsesAiProvider;
use App\ActivityType;
use App\Services\IdeaGeneratorService;
use Illuminate\Console\Command;

class GenerateIdea extends Command
{
    use UsesAiProvider;

    protected $signature = 'generate:idea
        {themes : Themes/topics comma-separated (e.g. "hewan darat, hewan dilindungi")}
        {type : Activity type (storytelling, komik, puzzle, etc)}
        {--count=10 : Number of ideas to generate}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--skills= : Skills to focus on, comma-separated (e.g. berani_bicara,mengelola_marah)}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model (run ai:provider <provider> to list)}';

    protected $description = 'Generate ideas from themes for a specific activity type using AI';

    public function handle(IdeaGeneratorService $service): int
    {
        $themes = array_map('trim', explode(',', $this->argument('themes')));
        $themes = array_filter($themes);
        $type = $this->argument('type');
        $count = (int) ($this->option('count') ?: 10);
        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $skills = $this->option('skills') ? array_map('trim', explode(',', $this->option('skills'))) : [];
        $theme = implode(', ', $themes);

        if (!ActivityType::tryFrom($type)) {
            $this->error("Unknown type: {$type}");
            $this->line("Available types:");
            foreach (ActivityType::cases() as $case) {
                $this->line("  <comment>{$case->value}</comment> — {$case->emoji()} {$case->description()}");
            }
            return self::FAILURE;
        }

        $this->info("=== Generating Ideas ===");
        $this->line("Themes : {$theme}");
        $this->line("Type   : {$type}");
        $this->line("Count  : {$count}");
        $this->line("Ages   : " . implode(',', $ages));
        $this->line("Agama  : " . ($agama ?: '-'));
        $this->line("Skills : " . (!empty($skills) ? implode(',', $skills) : '-'));

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;
        $this->newLine();

        try {
            $result = $service->generateWithAI($type, $count, $ages, $agama, $skills, $theme);
        } catch (\Exception $e) {
            $this->error("AI error: {$e->getMessage()}");
            return self::FAILURE;
        }

        $items = $result['items'] ?? [];
        if (empty($items)) {
            $this->error("AI returned invalid response. Try --count=5 or simpler themes.");
            return self::FAILURE;
        }

        $savedCount = $service->saveIdeas($result, $type, $ages, $agama, $skills, $count, $model);

        foreach ($items as $item) {
            $this->line("  {$item['name']}");
        }

        $this->newLine();
        $this->info("=== Done ===");
        $this->info("Saved {$savedCount} ideas from themes: {$theme}" . ($type ? " (type: {$type})" : ''));

        return self::SUCCESS;
    }

    private function parseAges(?string $input): array
    {
        if (empty($input)) {
            return range(3, 8);
        }

        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        $age = (int) $input;
        $min = max(1, $age - 1);
        $max = min(10, $age + 3);
        return range($min, $max);
    }
}
