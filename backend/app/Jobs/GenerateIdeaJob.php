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
                    $this->theme
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
                $model
            );

            Log::info('GenerateIdeaJob completed', [
                'type' => $mode,
                'saved' => $saved,
            ]);

        } catch (\Throwable $e) {
            Log::error('GenerateIdeaJob failed', [
                'type' => $mode,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function generateGlobal(AiService $ai, string $provider, string $model): array
    {
        $count = max(1, min(200, $this->count));

        $minAge = !empty($this->ages) ? min($this->ages) : 3;
        $maxAge = !empty($this->ages) ? max($this->ages) : 8;

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages like Chinese. DO NOT use difficult/foreign words like: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $skillLine = !empty($this->skills) ? "\nSkill focus: " . implode(', ', $this->skills) : '';
        $agamaLine = $this->agama ? "\nReligion: {$this->agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas based on theme: {$this->theme}

Each idea MUST be about a DIFFERENT topic/subject with DIFFERENT facts.

IMPORTANT RULES:
- Generate EXACTLY {$count} items, no more, no less
- Each item MUST have a UNIQUE name (no duplicates)
- Each item MUST have SPECIFIC factual details
- DO NOT use "si" in names
- DO NOT use character/person names
- DO NOT include location/place names in the name field

FORMAT for each field:
- name: just the topic name only, e.g. "Wortel", "Brokoli", "Paus Biru", "Komodo"
- desc: a comma-separated list of EXACTLY 10 attractive children's title ideas about that topic. Each title must be catchy, fun, and child-friendly. NO "si" prefix, NO character names, NO location names.
- info: factual information about the topic (2-3 sentences with specific details)

CORRECT examples:
- name: "Wortel"
- desc: "Wortel Manis Si Penjaga Mata, Kenalan Yuk Sama Wortel Oranye!, Si Jari-jari Manis Bikin Sehat, Ayo Gigit Wortel yang Kriuk!, Sahabat Mata Terang Si Kecil, Petualangan Wortel dari Kebun, Si Oranye Favorit Semua Anak, Rahasia Wortel Manis dari Tanah, Wortel Sahabat Perut Sehat, Si Kecil Penuh Vitamin A"
- info: "Menjaga kesehatan mata, kulit sehat, dan daya tahan tubuh kuat. Kaya beta-karoten dan vitamin A."

- name: "Paus Biru"
- desc: "Paus Biru Raksasa Samudra, Petualangan Paus di Lautan Dalam, Si Paus yang Bernyanyi, Paus Biru Hewan Terbesar, Ayo Kenalan Sama Paus Biru, Paus Biru Penjaga Laut, Rahasia Paus Biru yang Menakjubkan, Paus Biru dan Anaknya, Si Raksasa yang Lembut, Paus Biru Hewan Ajaib"
- info: "Hewan terbesar di dunia, panjangnya bisa mencapai 30 meter dan beratnya 200 ton. Jantungnya sebesar mobil kecil."

Age target: {$minAge}-{$maxAge} years old
{$skillLine}{$agamaLine}

Output in JSON array format:
[
  {
    "name": "Topic name only",
    "desc": "title1, title2, title3, ... (exactly 10 comma-separated attractive children's titles)",
    "info": "Factual information with specific details (2-3 sentences)"
  }
]

Only output JSON. All text must be in Indonesian.
PROMPT;

        $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

        if (!is_array($result)) {
            return ['items' => [], 'source' => 'ai'];
        }

        $items = [];
        foreach ($result as $item) {
            if (empty($item['name'])) continue;
            $items[] = [
                'name'  => $this->cleanText($item['name'] ?? ''),
                'desc'  => $this->cleanText($item['desc'] ?? ''),
                'moral' => $this->cleanText($item['info'] ?? $item['moral'] ?? ''),
            ];
        }

        return [
            'items'  => array_slice($items, 0, $count),
            'source' => 'ai',
            'prompt' => "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userPrompt}",
        ];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
