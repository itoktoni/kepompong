<?php

namespace App\Jobs;

use App\Models\Idea;
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
    public int $timeout = 300;

    public function __construct(
        public string $type,
        public string $theme,
        public int $count = 20,
        public array $ages = [],
        public array $skills = [],
        public ?string $agama = null,
        public ?string $provider = null,
        public ?string $jobId = null,
    ) {}

    public function handle(IdeaGeneratorService $service): void
    {
        Log::info('GenerateIdeaJob started', [
            'type' => $this->type,
            'theme' => $this->theme,
            'count' => $this->count,
        ]);

        try {
            $generator = $service->getGenerator($this->type);

            $result = $generator->generateWithAI(
                $this->count,
                $this->ages,
                $this->agama,
                $this->skills,
                $this->theme,
            );

            $source = $result['source'] ?? 'template';
            $items = $result['items'] ?? [];

            $saved = 0;
            foreach ($items as $item) {
                Idea::create([
                    'idea_nama'       => $item['name'],
                    'idea_keterangan' => $item['desc'],
                    'idea_moral'      => $item['moral'],
                    'idea_type'       => $this->type,
                    'idea_creator'    => $source === 'ai' ? config('ai.default_provider', 'openai') : 'template',
                    'idea_tanggal'    => null,
                    'idea_agama'      => $this->agama ? [$this->agama] : [],
                    'idea_ages'       => $this->ages,
                    'idea_skills'     => $this->skills,
                ]);
                $saved++;
            }

            Log::info('GenerateIdeaJob completed', [
                'type' => $this->type,
                'saved' => $saved,
                'title' => $result['title'] ?? null,
            ]);

            if ($this->jobId) {
                cache()->put("idea_job_{$this->jobId}", [
                    'status' => 'completed',
                    'saved'  => $saved,
                    'title'  => $result['title'] ?? 'Ide Aktivitas',
                    'items'  => $items,
                ], 3600);
            }

        } catch (\Throwable $e) {
            Log::error('GenerateIdeaJob failed', [
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);

            if ($this->jobId) {
                cache()->put("idea_job_{$this->jobId}", [
                    'status'  => 'failed',
                    'message' => $e->getMessage(),
                ], 3600);
            }
        }
    }
}
