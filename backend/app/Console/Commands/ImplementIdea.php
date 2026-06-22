<?php

namespace App\Console\Commands;

use App\ActivityType;
use App\Models\Idea;
use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class ImplementIdea extends Command
{
    protected $signature = 'implement:idea
        {id? : Idea ID from database (omit when using --automatic)}
        {--type= : Activity type (storytelling, bermain_peran, permainan, etc.)}
        {--count= : Number of activities to generate (default: idea_qty from DB)}
        {--automatic : Find all ideas without implementor and dispatch jobs for ALL types}';

    protected $description = 'Take an idea from DB and generate activities. With --automatic, loop ALL activity types per idea.';

    public function handle(ActivityGeneratorService $service): int
    {
        if ($this->option('automatic')) {
            return $this->runAutomatic();
        }

        return $this->runSingle();
    }

    private function runAutomatic(): int
    {
        $ideas = Idea::whereNull('idea_implementor')
            ->orderBy('idea_id', 'asc')
            ->limit(1)
            ->get();

        if ($ideas->isEmpty()) {
            $this->info("No pending ideas found (all have implementor).");
            return self::SUCCESS;
        }

        $types = ActivityType::cases();

        $this->info("=== Automatic Implement ===");
        $this->line("Found {$ideas->count()} pending ideas");
        $this->line("Will generate for " . count($types) . " types per idea");
        $this->line("Total jobs: " . ($ideas->count() * count($types)));
        $this->newLine();

        $dispatched = 0;
        foreach ($ideas as $idea) {
            foreach ($types as $type) {
                \App\Jobs\ImplementIdeaJob::dispatch($idea->idea_id, $type->value);
                $this->line("  [{$idea->idea_id}] {$idea->idea_nama} → {$type->emoji()} {$type->value}");
                $dispatched++;
            }
        }

        $this->newLine();
        $this->info("✓ Dispatched {$dispatched} jobs");

        return self::SUCCESS;
    }

    private function runSingle(): int
    {
        $id = $this->argument('id');

        if (!$id) {
            $this->error("Provide idea ID or use --automatic");
            $this->line("Usage:");
            $this->line("  php artisan implement:idea 746 --type=storytelling");
            $this->line("  php artisan implement:idea --automatic");
            return self::FAILURE;
        }

        $idea = Idea::find($id);

        if (!$idea) {
            $this->error("Idea #{$id} not found.");
            return self::FAILURE;
        }

        $type = $idea->idea_type ?: $this->option('type');

        if (!$type) {
            $available = array_keys(config('activity.types', []));
            $this->error("Idea #{$id} has no type.");
            $this->line("Set via: php artisan implement:idea {$id} --type=storytelling");
            $this->line("Available types: " . implode(', ', $available));
            return self::FAILURE;
        }

        if (!$idea->idea_type) {
            $idea->update(['idea_type' => $type]);
            $this->line("Set idea_type to: {$type}");
        }

        $count = (int) ($this->option('count') ?: $idea->idea_qty ?: 10);
        $config = config("activity.types.{$type}");

        if (!$config) {
            $available = array_keys(config('activity.types', []));
            $this->error("Unknown type: {$type}");
            $this->line("Available types: " . implode(', ', $available));
            return self::FAILURE;
        }

        $this->info("=== Implementing Idea #{$id} ===");
        $this->line("Type    : {$type}");
        $this->line("Theme   : {$idea->idea_nama}");
        $this->line("Count   : {$count}");
        $this->line("Prompt  : " . ($idea->idea_prompt ? '✓ tersimpan' : '✗ tidak ada'));
        $this->newLine();

        $saved = 0;
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $input = [
                'theme'      => $idea->idea_nama,
                'topic'      => $idea->idea_nama,
                'desc'       => $idea->idea_keterangan,
                'informasi'  => $idea->idea_informasi,
                'child'      => 'Anak',
                'pages'      => $config['default_pages'] ?? 16,
                'ages'       => $idea->idea_ages ?? [],
                'agama'      => !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
                'variation'  => $i + 1,
            ];

            try {
                $result = $service->generateContent($type, $input);
                $service->createActivity($type, $result, $input);
                $saved++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  Failed [{$i}]: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $idea->update([
            'idea_tanggal'     => now()->format('Y-m-d H:i:s'),
            'idea_implementor' => config('ai.default_provider'),
        ]);

        $this->info("✓ Done! Saved {$saved} activities from idea #{$id}");

        return self::SUCCESS;
    }
}
