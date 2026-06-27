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
        public ?string $type,
        public string $theme,
        public int $count = 10,
        public array $ages = [],
        public array $skills = [],
        public ?string $agama = null,
        public ?string $provider = null,
        public ?string $model = null,
        public ?int $createdBy = null,
        public int $pages = 9,
    ) {}

    public function handle(AiService $ai, IdeaGeneratorService $service): void
    {
        $mode = $this->type ?: 'global';
        Log::info('GenerateIdeaJob started', [
            'type' => $mode,
            'theme' => $this->theme,
            'count' => $this->count,
        ]);

        try {
            if (empty($this->ages)) {
                $this->ages = range(3, 8);
            }

            $provider = $this->provider ?: config('ai.default_provider');
            $model = $ai->getModel($provider, $this->model);

            if ($this->type) {
                $result = $service->generateWithAI(
                    $this->type,
                    $this->count,
                    $this->ages,
                    $this->agama,
                    $this->skills,
                    $this->theme,
                    $this->pages
                );
            } else {
                $result = $this->generateGlobal($ai, $provider, $model);
            }

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
                $model,
                $this->createdBy
            );

            Log::info('GenerateIdeaJob completed', [
                'type' => $mode,
                'saved' => $saved,
            ]);

            if ($this->createdBy) {
                \App\Http\Controllers\NotificationController::notify(
                    userId: $this->createdBy,
                    title: 'Ide Selesai Dibuat',
                    body: "{$saved} ide \"{$this->theme}\" sudah siap.",
                    icon: '💡',
                    iconColor: '#176c33',
                    type: 'idea',
                    url: null,
                );
            }

            if ($this->type && $saved > 0) {
                $ideaIds = \App\Models\Idea::where('created_by', $this->createdBy)
                    ->where('idea_type', $this->type)
                    ->orderBy('idea_id', 'desc')
                    ->limit($saved)
                    ->pluck('idea_id')
                    ->toArray();

                foreach ($ideaIds as $ideaId) {
                    ImplementIdeaJob::dispatch(
                        ideaId: $ideaId,
                        type: $this->type,
                        count: 1,
                        notes: null,
                        skills: $this->skills,
                        pages: $this->pages,
                    )->onQueue('default');
                    Log::info('GenerateIdeaJob dispatched ImplementIdeaJob', ['idea_id' => $ideaId]);
                }
            }

        } catch (\Throwable $e) {
            Log::error('GenerateIdeaJob failed', [
                'type' => $mode,
                'error' => $e->getMessage(),
            ]);

            if ($this->createdBy) {
                \App\Http\Controllers\NotificationController::notify(
                    userId: $this->createdBy,
                    title: 'Gagal Membuat Ide',
                    body: "Terjadi kesalahan saat membuat ide \"{$this->theme}\".",
                    icon: '❌',
                    iconColor: '#C62828',
                    type: 'error',
                );
            }
        }
    }

    private function generateGlobal(AiService $ai, string $provider, string $model): array
    {
        $count = max(1, min(200, $this->count));

        $minAge = !empty($this->ages) ? min($this->ages) : 3;
        $maxAge = !empty($this->ages) ? max($this->ages) : 8;

        $systemPrompt = <<<PROMPT
Kamu adalah penulis cerita anak Indonesia profesional.
Buat ide cerita yang menarik dan sesuai tema.
Gunakan HANYA bahasa Indonesia sederhana untuk anak usia {$minAge}-{$maxAge} tahun.
Jangan gunakan kata sulit atau bahasa asing.
Output dalam format JSON array.
PROMPT;

        $skillLine = !empty($this->skills) ? "\nSkill/nilai: " . implode(', ', $this->skills) : '';
        $agamaLine = $this->agama ? "\nKonteks agama: {$this->agama}" : '';

        $userPrompt = <<<PROMPT
Buatkan EXACTLY {$count} ide cerita/aktivitas anak berdasarkan tema:

TEMA: {$this->theme}

PENTING:
- Jika tema menyebut nama karakter, GUNAKAN nama itu sebagai tokoh utama
- Jika tema menyebut situasi, JADIKAN itu alur cerita
- Jika tema menyebut tempat, JADIKAN itu latar cerita
- Setiap ide harus berbeda: beda konflik, beda alur, beda ending

Setiap ide harus punya 3 field:
- name: judul cerita yang menarik dan catchy (satu kalimat pendek)
- desc: rencana cerita LENGKAP ditulis dalam kalimat natural mengalir. TIDAK BOLEH gunakan format "Tokoh:", "Latar:", "Alur:", "Ending:" atau tanda kurung (). Tulis seperti bercerita biasa.
- info: pelajaran moral dari cerita (satu kalimat)

CONTOH:
- name: "Faqih dan Ikan Marlin yang Ajaib"
- desc: "Pagi hari di pantai selatan Jawa, Faqih diajak Kakek yang sudah berpengalaman memancing. Mereka mancing seharian tapi tidak dapat ikan sama sekali. Faqih hampir menyerah tapi Kakek mengajarkan mengganti umpan dari cacing ke udang. Tiba-tiba kail ditarik sangat kuat oleh ikan marlin besar! Faqih dan Kakek bekerja sama menarik ikan itu. Akhirnya Faqih berhasil dan belajar bahwa kesabaran membuahkan hasil."
- info: "Kesabaran dan pantang menyerah akan membuahkan hasil."

{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "name": "Judul cerita",
    "desc": "Rencana cerita lengkap dalam kalimat natural mengalir",
    "info": "Pelajaran moral"
  }
]

HANYA output JSON. Semua teks dalam bahasa Indonesia.
PROMPT;

        $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

        if (!is_array($result)) {
            return ['items' => [], 'source' => 'ai'];
        }

        $items = [];
        foreach ($result as $item) {
            if (empty($item['name'])) continue;
            $items[] = [
                'name'  => $item['name'] ?? '',
                'desc'  => $item['desc'] ?? '',
                'moral' => $item['info'] ?? $item['moral'] ?? '',
            ];
        }

        return [
            'items'  => array_slice($items, 0, $count),
            'source' => 'ai',
            'prompt' => "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userPrompt}",
        ];
    }
}
