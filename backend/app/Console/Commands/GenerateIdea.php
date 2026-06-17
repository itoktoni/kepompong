<?php

namespace App\Console\Commands;

use App\Models\Idea;
use App\Services\IdeaGeneratorService;
use Illuminate\Console\Command;

class GenerateIdea extends Command
{
    protected $signature = 'generate:idea
        {type : Game type (e.g. permainan_edukasi, permainan_kerjasama, permainan_aktif)}
        {--count=8 : Number of ideas to generate}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--skills= : Skills to focus on, comma-separated (e.g. berani_bicara,mengelola_marah)}';

    protected $description = 'Generate game ideas with AI and save to database';

    public function handle(IdeaGeneratorService $service): int
    {
        $type = $this->argument('type');
        $count = (int) ($this->option('count') ?: 8);
        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $skills = $this->option('skills') ? array_map('trim', explode(',', $this->option('skills'))) : [];

        $this->info("Generating game ideas with AI...");
        $this->line("Type   : {$type}");
        $this->line("Count  : {$count}");
        $this->line("Ages   : " . implode(',', $ages));
        $this->line("Agama  : " . ($agama ?: '-'));
        $this->line("Skills : " . (!empty($skills) ? implode(',', $skills) : '-'));
        $this->newLine();

        $result = $service->generateWithAI($type, $count, $ages, $agama, $skills);

        $collectionTitle = $result['title'];
        $items = $result['items'] ?? [];
        $source = $result['source'] ?? 'template';

        $savedCount = 0;

        foreach ($items as $item) {
            $idea = Idea::create([
                'idea_nama' => $item['name'],
                'idea_keterangan' => $item['desc'],
                'idea_moral' => $item['moral'],
                'idea_type' => str_replace('permainan_', '', $type),
                'idea_ai' => $source === 'ai' ? config('services.openai.model', 'MiniMax-M2.7-highspeed') : 'template',
                'idea_tanggal' => null,
                'idea_agama' => $agama ? [$agama] : [],
                'idea_ages' => $ages,
                'idea_skills' => $skills,
            ]);

            $savedCount++;
            $this->line("  [{$item['num']}] {$item['name']}");
        }

        $this->newLine();
        $this->info("=== {$collectionTitle} ===");
        $this->info("Saved {$savedCount} ideas to database!");
        $this->info("AI Source: " . ($source === 'ai' ? 'OpenAI' : 'Template'));

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
