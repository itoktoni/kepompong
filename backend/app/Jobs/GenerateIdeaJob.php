<?php

namespace App\Jobs;

use App\ActivityType;
use App\Models\Idea;
use App\Services\AiService;
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
        public int $count = 10,
        public array $ages = [],
        public array $skills = [],
        public ?string $agama = null,
        public ?string $provider = null,
        public ?string $model = null,
    ) {}

    public function handle(AiService $ai): void
    {
        Log::info('GenerateIdeaJob started', [
            'type' => $this->type,
            'theme' => $this->theme,
            'count' => $this->count,
        ]);

        try {
            $themes = array_map('trim', explode(',', $this->theme));
            $themes = array_filter($themes);

            if (empty($this->ages)) {
                $this->ages = range(3, 8);
            }

            $provider = $this->provider ?: config('ai.default_provider');
            $model = $ai->getModel($provider, $this->model);

            $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing seperti: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Gunakan kata sederhana: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output harus dalam format JSON array.';
            $userPrompt = $this->buildPrompt($themes);

            $fullPrompt = "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userPrompt}";

            $items = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($items) || empty($items)) {
                Log::warning('GenerateIdeaJob: AI returned invalid response');
                return;
            }

            $items = array_filter($items, fn ($item) => isset($item['topik']) && isset($item['fakta']));
            $items = array_values($items);

            $saved = 0;
            foreach ($items as $item) {
                Idea::create([
                    'idea_nama'       => $item['topik'],
                    'idea_keterangan' => $item['fakta'],
                    'idea_moral'      => $item['moral'] ?? '',
                    'idea_type'       => $this->type,
                    'idea_creator'    => $model,
                    'idea_tanggal'    => null,
                    'idea_agama'      => $this->agama ? [$this->agama] : [],
                    'idea_ages'       => $this->ages,
                    'idea_skills'     => $this->skills,
                    'idea_qty'        => $this->count,
                    'idea_prompt'     => $fullPrompt,
                ]);
                $saved++;
            }

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

    private function buildPrompt(array $themes): string
    {
        $themeList = implode(', ', $themes);
        $ageRange = min($this->ages) . '-' . max($this->ages);
        $skillLine = !empty($this->skills) ? "\nFokus skill: " . implode(', ', $this->skills) : '';
        $agamaLine = $this->agama ? "\nAgama: {$this->agama}" : '';

        $typeGuide = match ($this->type) {
            'storytelling' => 'Buat ide cerita dengan karakter utama, konflik, dan penyelesaian.',
            'bermain_peran' => 'Buat ide skenario bermain peran dengan peran dan situasi yang menarik.',
            'permainan' => 'Buat ide permainan seru dengan aturan sederhana.',
            'monolog' => 'Buat ide naskah monolog dengan karakter dan tema.',
            'proyek_kreatif' => 'Buat ide proyek seni dan kerajinan.',
            'musik_gerak' => 'Buat ide lagu dan gerakan untuk anak.',
            'puzzle' => 'Buat ide puzzle dan teka-teki.',
            'mindfulness' => 'Buat ide latihan mindfulness.',
            'outdoor' => 'Buat ide eksplorasi outdoor.',
            'ilmu_pengetahuan' => 'Buat ide eksperimen sains sederhana.',
            'tebak_teakan' => 'Buat ide tebak-tebakan.',
            'permainan_tangan' => 'Buat ide permainan jari dan tangan.',
            'latihan_otak' => 'Buat ide latihan otak.',
            'komik' => 'Buat ide komik dengan karakter dan dialog.',
            'worksheet' => 'Buat ide worksheet edukatif.',
            'coloring' => 'Buat ide halaman mewarnai.',
            default => 'Buat ide konten kreatif untuk anak.',
        };

        return <<<PROMPT
Buatlah {$this->count} ide untuk konten bertipe "{$this->type}" ({$typeGuide}), berdasarkan tema: {$themeList}

Format: Nama > Tempat > Fakta spesifik

Contoh:
- "Komodo > Pulau Komodo > punya air liur berbahaya, suatu hari seekor komodo muda bernama Komo tersesat"
- "Burung Cendrawasih > Papua > bulunya sangat indah, seekor burung cendrawasih bernama Candra belajar menari"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Nama > Tempat > Fakta singkat",
    "fakta": "Detail lengkap (3-5 kalimat)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;
    }
}
