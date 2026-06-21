<?php

namespace App\Jobs;

use App\Services\AiService;
use App\Services\IdeaGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateIdeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 600;

    public function __construct(
        public string $type,
        public string $theme,
        public int $count = 100,
        public array $ages = [],
        public array $skills = [],
        public ?string $agama = null,
        public ?string $provider = null,
        public ?string $model = null,
    ) {}

    public function handle(AiService $ai, IdeaGeneratorService $service): void
    {
        Log::info('GenerateIdeaJob started', [
            'type' => $this->type,
            'theme' => $this->theme,
            'count' => $this->count,
        ]);

        try {
            if (empty($this->ages)) {
                $this->ages = range(3, 8);
            }

            $provider = $this->provider ?: config('ai.default_provider');
            $model = $ai->getModel($provider, $this->model);

            $result = $service->generateWithAI(
                $this->type,
                $this->count,
                $this->ages,
                $this->agama,
                $this->skills,
                $this->theme
            );

            $items = $result['items'] ?? [];
            if (empty($items)) {
                Log::warning('GenerateIdeaJob: AI returned invalid response');
                return;
            }

            $saved = $service->saveIdeas(
                $result,
                $this->type,
                $this->ages,
                $this->agama,
                $this->skills,
                $this->count,
                $model
            );

            Log::info('GenerateIdeaJob completed', [
                'type' => $this->type,
                'saved' => $saved,
            ]);

        } catch (\Throwable $e) {
            Log::error('GenerateIdeaJob failed', [
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
