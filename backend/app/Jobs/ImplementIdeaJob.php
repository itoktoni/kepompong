<?php

namespace App\Jobs;

use App\Models\Idea;
use App\Services\ActivityGeneratorService;
use App\Services\ActivityImageService;
use App\Console\Commands\GenerateImage;
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
        public int $pages = 8,
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
                        'pages'      => $this->pages,
                        'ages'       => $idea->idea_ages ?? [],
                        'agama'      => !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
                        'variation'  => $i + 1,
                        'original_theme' => $idea->idea_prompt ? '' : $idea->idea_nama,
                        'created_by' => $idea->created_by ?? 1,
                    ];

                try {
                    $result = $service->generateContent($type, $input);
                    $activity = $service->createActivity($type, $result, $input);
                    $saved++;
                    Log::info("ImplementIdeaJob [{$type}] [{$skill}] {$saved}", ['title' => $result['title'] ?? '']);

                    if(config('ai.image.api_key'))
                    {
                        $this->generateImage($activity);
                    }

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
                    'pages'      => $this->pages,
                    'ages'       => $idea->idea_ages ?? [],
                    'agama'      => !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
                    'variation'  => $i + 1,
                    'original_theme' => $idea->idea_prompt ? '' : $idea->idea_nama,
                    'created_by' => $idea->created_by ?? 1,
                ];

                try {
                    $result = $service->generateContent($type, $input);
                    Log::info($result);
                    $activity = $service->createActivity($type, $result, $input);
                    Log::info($activity);
                    $saved++;
                    Log::info("ImplementIdeaJob [{$type}] {$saved}/{$count}", ['title' => $result['title'] ?? '']);

                    if(config('ai.image.api_key'))
                    {
                        $this->generateImage($activity);
                    }

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

        if ($saved > 0 && $idea->created_by) {
            \App\Http\Controllers\NotificationController::notify(
                userId: $idea->created_by,
                title: 'Aktivitas Selesai Dibuat',
                body: "{$saved} aktivitas \"{$idea->idea_nama}\" ({$type}) sudah siap.",
                icon: '✅',
                iconColor: '#176c33',
                type: 'activity',
                url: null,
            );
        } elseif ($saved === 0 && $idea->created_by) {
            \App\Http\Controllers\NotificationController::notify(
                userId: $idea->created_by,
                title: 'Gagal Membuat Aktivitas',
                body: "Tidak ada aktivitas yang berhasil dibuat dari \"{$idea->idea_nama}\" ({$type}).",
                icon: '❌',
                iconColor: '#C62828',
                type: 'error',
            );
        }
    }

    private function generateImage($activity): void
    {
        if (!$activity || !$activity->id) return;

        try {
            $cmd = app(GenerateImage::class);
            $imageService = app(ActivityImageService::class);

            $prompt = $cmd->buildPrompt($activity);
            if ($prompt) {
                $activity->prompt = $prompt;
                $activity->save();
                Log::info("ImplementIdeaJob image prompt saved", ['activity_id' => $activity->id]);
            }

            if (!empty($activity->prompt)) {
                $result = $imageService->process($activity, '2K', null, null, false);
                Log::info("ImplementIdeaJob image generated", [
                    'activity_id' => $activity->id,
                    'status' => $result['status'] ?? 'unknown',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("ImplementIdeaJob image generation failed (non-fatal)", [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
