<?php

namespace App\Console\Commands;

use App\Models\Idea;
use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class ImplementIdea extends Command
{
    protected $signature = 'implement:idea
        {id : Idea ID from database}
        {--count= : Number of activities to generate (default: idea_qty from DB)}';

    protected $description = 'Take an idea from DB and generate activities using ActivityGeneratorService';

    public function handle(ActivityGeneratorService $service): int
    {
        $id = $this->argument('id');
        $idea = Idea::find($id);

        if (!$idea) {
            $this->error("Idea #{$id} not found.");
            return self::FAILURE;
        }

        if (!$idea->idea_type) {
            $this->error("Idea #{$id} has no type. Set idea_type first.");
            return self::FAILURE;
        }

        $count = (int) ($this->option('count') ?: $idea->idea_qty ?: 10);
        $type = $idea->idea_type;
        $config = config("activity.types.{$type}");

        if (!$config) {
            $this->error("Unknown type: {$type}");
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
                'theme' => $idea->idea_nama,
                'topic' => $idea->idea_nama,
                'child' => 'Anak',
                'pages' => $config['default_pages'] ?? 16,
                'ages'  => $idea->idea_ages ?? [],
                'agama' => !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
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
