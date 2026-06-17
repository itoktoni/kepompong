<?php

namespace App\Jobs;

use App\Services\ActivityGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(
        public string $type,
        public string $theme,
        public ?string $child = null,
        public ?int $pages = null,
        public array $ages = [],
        public ?string $agama = null,
        public ?string $subtopic = null,
        public ?string $style = null,
        public ?string $grades = null,
    ) {}

    public function handle(ActivityGeneratorService $service): void
    {
        Log::info('GenerateActivityJob started', [
            'type' => $this->type,
            'theme' => $this->theme,
        ]);

        try {
            $config = config("activity.types.{$this->type}");
            $pages = $this->pages ?? ($config['default_pages'] ?? 16);

            $input = [
                'theme'    => $this->theme,
                'topic'    => $this->theme,
                'child'    => $this->child ?: 'Anak',
                'pages'    => $pages,
                'ages'     => $this->ages,
                'agama'    => $this->agama,
                'subtopic' => $this->subtopic,
                'style'    => $this->style,
                'grades'   => $this->grades ? explode(',', $this->grades) : [1],
            ];

            Log::info('GenerateActivityJob process', json_encode($input));

            $result = $service->generateContent($this->type, $input);
            $activity = $service->createActivity($this->type, $result, $input);

            Log::info('GenerateActivityJob completed', [
                'type'       => $this->type,
                'activity_id' => $activity->id,
                'title'      => $activity->title,
            ]);

        } catch (\Throwable $e) {
            Log::error('GenerateActivityJob failed', [
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
