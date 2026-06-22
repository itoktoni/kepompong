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
        public ?string $type = null,
        public ?int $count = null,
        public ?string $notes = null,
        public array $skills = [],
    ) {}

    public function handle(ActivityGeneratorService $service): void
    {
        $idea = Idea::find($this->ideaId);

        if (!$idea) {
            Log::warning('ImplementIdeaJob: idea not found', ['idea_id' => $this->ideaId]);
            return;
        }

        $type = $this->type ?? $idea->idea_type;

        if (!$type) {
            Log::warning('ImplementIdeaJob: no type', ['idea_id' => $this->ideaId]);
            return;
        }

        $config = config("activity.types.{$type}");

        if (!$config) {
            Log::warning('ImplementIdeaJob: unknown type', ['type' => $type]);
            return;
        }

        $count = $this->count ?? $idea->idea_qty ?? 10;
        $skills = !empty($this->skills) ? $this->skills : ($idea->idea_skills ?? []);

        Log::info('ImplementIdeaJob started', [
            'idea_id' => $this->ideaId,
            'type'    => $type,
            'count'   => $count,
            'skills'  => $skills,
        ]);

        $saved = 0;

        if (!empty($skills)) {
            foreach ($skills as $skillIndex => $skill) {
                for ($i = 0; $i < $count; $i++) {
                    $input = [
                        'theme'      => $idea->idea_nama,
                        'topic'      => $idea->idea_nama,
                        'desc'       => $idea->idea_keterangan,
                        'informasi'  => $idea->idea_informasi,
                        'notes'      => $this->notes,
                        'skill'      => $skill,
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
                        Log::info("ImplementIdeaJob [{$type}] [{$skill}] {$saved}", ['title' => $result['title'] ?? '']);
                    } catch (\Throwable $e) {
                        Log::error("ImplementIdeaJob [{$type}] [{$skill}] failed", [
                            'iteration' => $i + 1,
                            'error'     => $e->getMessage(),
                        ]);
                    }
                }
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                $input = [
                    'theme'      => $idea->idea_nama,
                    'topic'      => $idea->idea_nama,
                    'desc'       => $idea->idea_keterangan,
                    'informasi'  => $idea->idea_informasi,
                    'notes'      => $this->notes,
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
                    Log::info("ImplementIdeaJob [{$type}] {$saved}/{$count}", ['title' => $result['title'] ?? '']);
                } catch (\Throwable $e) {
                    Log::error("ImplementIdeaJob [{$type}] failed", [
                        'iteration' => $i + 1,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }
        }

        if (!$this->type) {
            $idea->update([
                'idea_tanggal'     => now()->format('Y-m-d H:i:s'),
                'idea_implementor' => config('ai.default_provider'),
            ]);
        }

        Log::info('ImplementIdeaJob completed', [
            'idea_id' => $this->ideaId,
            'type'    => $type,
            'saved'   => $saved,
        ]);
    }
}
