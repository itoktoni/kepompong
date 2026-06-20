<?php

namespace App\Console\Commands;

use App\Console\Concerns\UsesAiProvider;
use App\Models\Idea;
use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class ImplementIdea extends Command
{
    use UsesAiProvider;

    protected $signature = 'implement:idea
        {id : Idea ID from database}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model}';

    protected $description = 'Take an idea from DB and generate activity content using AI';

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

        $type = $idea->idea_type;
        $theme = $idea->idea_nama;

        $config = config("activity.types.{$type}");
        if (!$config) {
            $this->error("Unknown type: {$type}");
            return self::FAILURE;
        }

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;

        $this->info("=== Implementing Idea #{$id} ===");
        $this->line("Type   : {$type}");
        $this->line("Theme  : {$theme}");
        $this->line("Ages   : " . implode(',', $idea->idea_ages ?? []));
        $this->line("AI     : {$provider} / {$model}");
        $this->newLine();

        $input = [
            'theme'    => $theme,
            'topic'    => $theme,
            'child'    => 'Anak',
            'pages'    => $config['default_pages'] ?? 16,
            'ages'     => $idea->idea_ages ?? [],
            'agama'    => !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
        ];

        $this->line("Calling AI...");
        $result = $service->generateContent($type, $input);

        if (empty($result['title'])) {
            $this->error("AI returned empty result.");
            return self::FAILURE;
        }

        $this->info("Title: {$result['title']}");

        $this->line("Saving to database...");
        $activity = $service->createActivity($type, $result, $input);

        $idea->update([
            'idea_tanggal'    => now()->format('Y-m-d H:i:s'),
            'idea_implementor' => "{$provider}/{$model}",
        ]);

        $this->info("✓ Done! Activity ID: {$activity->id}");
        $this->line("  Type  : {$activity->type}");
        $this->line("  Title : {$activity->title}");
        $this->line("  Slug  : {$activity->slug}");

        return self::SUCCESS;
    }
}
