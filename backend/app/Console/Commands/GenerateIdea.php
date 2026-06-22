<?php

namespace App\Console\Commands;

use App\Console\Concerns\UsesAiProvider;
use App\ActivityType;
use App\Services\AiService;
use App\Services\IdeaGeneratorService;
use Illuminate\Console\Command;

class GenerateIdea extends Command
{
    use UsesAiProvider;

    protected $signature = 'generate:idea
        {themes : Themes/topics comma-separated (e.g. "wortel, brokoli, tomat")}
        {type? : Activity type (storytelling, komik, puzzle, etc). Empty for global ideas}
        {--count=10 : Number of ideas to generate}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--skills= : Skills to focus on, comma-separated (e.g. berani_bicara,mengelola_marah)}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model (run ai:provider <provider> to list)}';

    protected $description = 'Generate ideas from themes. Without type = global ideas (like seeder data). With type = type-specific ideas.';

    public function handle(IdeaGeneratorService $service): int
    {
        $themes = array_map('trim', explode(',', $this->argument('themes')));
        $themes = array_filter($themes);
        $type = $this->argument('type');
        $count = (int) ($this->option('count') ?: 10);
        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $skills = $this->option('skills') ? array_map('trim', explode(',', $this->option('skills'))) : [];
        $theme = implode(', ', $themes);

        if ($type && !ActivityType::tryFrom($type)) {
            $this->error("Unknown type: {$type}");
            $this->line("Available types:");
            foreach (ActivityType::cases() as $case) {
                $this->line("  <comment>{$case->value}</comment> — {$case->emoji()} {$case->description()}");
            }
            return self::FAILURE;
        }

        $mode = $type ? "type-specific ({$type})" : 'global';
        $this->info("=== Generating Ideas ({$mode}) ===");
        $this->line("Themes : {$theme}");
        $this->line("Type   : " . ($type ?: 'global (no type)'));
        $this->line("Count  : {$count}");
        $this->line("Ages   : " . implode(',', $ages));
        $this->line("Agama  : " . ($agama ?: '-'));
        $this->line("Skills : " . (!empty($skills) ? implode(',', $skills) : '-'));

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;
        $this->newLine();

        try {
            if ($type) {
                $result = $service->generateWithAI($type, $count, $ages, $agama, $skills, $theme);
            } else {
                $result = $this->generateGlobal($ai, $provider, $model, $count, $ages, $agama, $skills, $theme);
            }
        } catch (\Exception $e) {
            $this->error("AI error: {$e->getMessage()}");
            return self::FAILURE;
        }

        $items = $result['items'] ?? [];
        if (empty($items)) {
            $this->error("AI returned invalid response. Try --count=5 or simpler themes.");
            return self::FAILURE;
        }

        $savedCount = $this->saveIdeas($result, $type, $ages, $agama, $skills, $count, $model);

        foreach ($items as $item) {
            $this->line("  [{$item['name']}] → {$item['desc']}");
        }

        $this->newLine();
        $this->info("=== Done ===");
        $this->info("Saved {$savedCount} ideas from themes: {$theme}" . ($type ? " (type: {$type})" : ' (global)'));

        return self::SUCCESS;
    }

    private function generateGlobal(AiService $ai, string $provider, string $model, int $count, array $ages, ?string $agama, array $skills, string $theme): array
    {
        $count = max(1, min(200, $count));

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $systemPrompt = 'You are a creative idea generator for Indonesian children. Use ONLY Indonesian language with Latin alphabet. DO NOT use other languages like Chinese. DO NOT use difficult/foreign words like: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Use simple words: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output must be in JSON array format.';

        $skillLine = !empty($skills) ? "\nSkill focus: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nReligion: {$agama}" : '';

        $userPrompt = <<<PROMPT
Generate EXACTLY {$count} UNIQUE ideas based on theme: {$theme}

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

    private function saveIdeas(array $result, ?string $type, array $ages, ?string $agama, array $skills, int $count, string $model): int
    {
        $items = $result['items'] ?? [];
        $prompt = $result['prompt'] ?? '';

        $saved = 0;
        foreach ($items as $item) {
            \App\Models\Idea::create([
                'idea_nama'       => $item['name'] ?? '',
                'idea_keterangan' => $item['desc'] ?? '',
                'idea_informasi'  => $item['moral'] ?? '',
                'idea_type'       => $type,
                'idea_creator'    => $model,
                'idea_tanggal'    => null,
                'idea_agama'      => $agama ? [$agama] : [],
                'idea_ages'       => $ages,
                'idea_skills'     => $skills,
                'idea_qty'        => $count,
                'idea_prompt'     => $prompt,
            ]);
            $saved++;
        }

        return $saved;
    }

    private function parseAges(?string $input): array
    {
        if (empty($input)) {
            return range(3, 8);
        }

        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        $age = (int) $input;
        $min = max(1, $age - 1);
        $max = min(10, $age + 3);
        return range($min, $max);
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
