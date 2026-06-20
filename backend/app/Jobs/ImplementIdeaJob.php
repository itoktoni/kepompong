<?php

namespace App\Jobs;

use App\Models\Idea;
use App\Services\ActivityGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImplementIdeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 600;

    public function __construct(
        public int $ideaId,
        public ?int $count = null,
    ) {}

    public function handle(ActivityGeneratorService $service): void
    {
        $idea = Idea::findOrFail($this->ideaId);
        $count = $this->count ?? $idea->idea_qty ?? 10;

        Log::info('ImplementIdeaJob started', ['idea_id' => $this->ideaId, 'count' => $count]);

        if (!$idea->idea_type) {
            Log::warning('ImplementIdeaJob: idea has no type', ['idea_id' => $this->ideaId]);
            return;
        }

        $type = $idea->idea_type;
        $config = config("activity.types.{$type}");

        if (!$config) {
            Log::warning('ImplementIdeaJob: unknown type', ['type' => $type]);
            return;
        }

        $saved = 0;
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
                Log::info("ImplementIdeaJob activity {$saved}/{$count}", ['title' => $result['title'] ?? '']);
            } catch (\Throwable $e) {
                Log::error("ImplementIdeaJob activity failed", [
                    'iteration' => $i + 1,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $idea->update([
            'idea_tanggal'     => now()->format('Y-m-d H:i:s'),
            'idea_implementor' => config('ai.default_provider'),
        ]);

        Log::info('ImplementIdeaJob completed', [
            'idea_id' => $this->ideaId,
            'saved'   => $saved,
            'total'   => $count,
        ]);
    }
}
