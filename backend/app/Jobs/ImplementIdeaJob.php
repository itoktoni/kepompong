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
    public int $timeout = 300;

    public function __construct(
        public int $ideaId,
    ) {}

    public function handle(ActivityGeneratorService $service): void
    {
        Log::info('ImplementIdeaJob started', ['idea_id' => $this->ideaId]);

        try {
            $idea = Idea::findOrFail($this->ideaId);

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

            $input = [
                'theme'    => $idea->idea_nama,
                'topic'    => $idea->idea_nama,
                'child'    => 'Anak',
                'pages'    => $config['default_pages'] ?? 16,
                'ages'     => $idea->idea_ages ?? [],
                'agama'    => !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
            ];

            $result = $service->generateContent($type, $input);
            $activity = $service->createActivity($type, $result, $input);

            $idea->update([
                'idea_tanggal'     => now()->format('Y-m-d H:i:s'),
                'idea_implementor' => config('ai.default_provider', 'ai'),
            ]);

            Log::info('ImplementIdeaJob completed', [
                'idea_id'     => $this->ideaId,
                'activity_id' => $activity->id,
                'title'       => $activity->title,
            ]);

        } catch (\Throwable $e) {
            Log::error('ImplementIdeaJob failed', [
                'idea_id' => $this->ideaId,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
