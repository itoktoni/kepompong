<?php

namespace App\Console\Commands;

use App\ActivityType;
use App\Console\Concerns\UsesAiProvider;
use App\Models\Pilar;
use App\Services\ActivityGeneratorService;
use App\Services\AiService;
use Illuminate\Console\Command;

class GenerateActivity extends Command
{
    use UsesAiProvider;

    protected $signature = 'generate:activity
        {type : Activity type}
        {theme? : Theme / topic / subject for the activity (auto-generated if empty)}
        {--child= : Child name (auto-generated if empty)}
        {--pages= : Number of pages/panels}
        {--ages= : Target ages (e.g. 7 or 3,4,5,6,7,8)}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--subtopic= : Worksheet subtopic (e.g. penjumlahan, huruf)}
        {--grades= : Worksheet grades (e.g. 1 or 1,2,3)}
        {--style= : Coloring style (simple, detailed, mandala) or worksheet type (practice, exam, activity)}
        {--pilar= : Pilar key (e.g. spiritual, karakter, kreatifitas, disiplin, kemandirian, sosial, emosi, kesehatan)}
        {--count=1 : Number of activities to generate}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model (run ai:provider <provider> to list)}';

    protected $description = 'Generate activity content with AI (story, comic, coloring, worksheet)';

    public function __construct()
    {
        $types = implode(', ', array_column(ActivityType::cases(), 'value'));
        $this->signature = str_replace(
            '{type : Activity type}',
            "{type : Activity type: {$types}}",
            $this->signature
        );
        parent::__construct();
    }

    public function handle(ActivityGeneratorService $service): int
    {
        $type = $this->argument('type');
        $theme = $this->argument('theme');

        $config = config("activity.types.{$type}");
        if (!$config) {
            $this->error("Unknown type: {$type}");
            $this->line("Available types:");
            foreach (ActivityType::cases() as $case) {
                $cfg = config("activity.types.{$case->value}");
                $emoji = $cfg['emoji'] ?? $case->emoji();
                $label = $cfg['label'] ?? $case->description();
                $this->line("  <comment>{$case->value}</comment> — {$emoji} {$label}");
            }
            return self::FAILURE;
        }

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;

        $count = max(1, (int) ($this->option('count') ?: 1));
        $pilarKey = $this->option('pilar');

        $input = $this->buildInput($type, $theme ?: '', $config);
        if ($pilarKey) {
            $input['pilar'] = $pilarKey;
        }

        if ($count > 1) {
            return $this->handleBatch($service, $type, $config, $input, $count);
        }

        if (empty($theme)) {
            $this->line("Theme not provided, generating with AI...");
            $theme = $this->generateTheme($ai, $type, $config, $pilarKey);
            if (!$theme) {
                $this->error("Failed to generate theme.");
                return self::FAILURE;
            }
            $this->info("Generated theme: {$theme}");
            $input = $this->buildInput($type, $theme, $config);
            if ($pilarKey) $input['pilar'] = $pilarKey;
        }

        $this->info("=== Generating {$config['emoji']} {$config['label']} ===");
        $this->line("Argument : {$theme}");
        foreach ($input as $key => $value) {
            if (in_array($key, ['theme', 'topic'])) continue;
            $display = is_array($value) ? implode(',', $value) : ($value ?: '-');
            $this->line("  {$key} : {$display}");
        }

        $this->newLine();
        $this->line("Calling AI...");
        $result = $service->generateContent($type, $input);

        if (empty($result['title'])) {
            $this->error("AI returned empty result.");
            return self::FAILURE;
        }

        $this->info("Title: {$result['title']}");
        if (!empty($result['desc'])) {
            $this->line("Desc:  {$result['desc']}");
        }
        if (!empty($result['moral'])) {
            $this->comment("Moral: {$result['moral']}");
        }

        $this->line("Saving to database...");
        $activity = $service->createActivity($type, $result, $input);

        $this->info("✓ Saved! Activity ID: {$activity->id}");
        $this->line("  Type  : {$activity->type}");
        $this->line("  Slug  : {$activity->slug}");
        $this->line("  Image : images/{$activity->type}/{$activity->slug}/");

        return self::SUCCESS;
    }

    private function handleBatch(ActivityGeneratorService $service, string $type, array $config, array $input, int $count): int
    {
        $this->info("=== Generating {$count}x {$config['emoji']} {$config['label']} (1 API call) ===");
        $this->line("Count  : {$count}");
        foreach ($input as $key => $value) {
            if (in_array($key, ['theme', 'topic'])) continue;
            $display = is_array($value) ? implode(',', $value) : ($value ?: '-');
            $this->line("  {$key} : {$display}");
        }

        $this->newLine();
        $this->line("Calling AI (batch)...");
        $results = $service->generateBatchContent($type, $count, $input);

        if (empty($results)) {
            $this->error("AI returned empty results.");
            return self::FAILURE;
        }

        $successCount = 0;
        foreach ($results as $i => $result) {
            $num = $i + 1;
            $this->newLine();
            $this->info("[{$num}] Title: {$result['title']}");
            if (!empty($result['desc'])) {
                $this->line("    Desc:  {$result['desc']}");
            }
            if (!empty($result['moral'])) {
                $this->comment("    Moral: {$result['moral']}");
            }

            $this->line("    Saving...");
            $activity = $service->createActivity($type, $result, $input);
            $this->info("    ✓ ID: {$activity->id} | Slug: {$activity->slug}");
            $successCount++;
        }

        $this->newLine();
        $this->info("=== Done: {$successCount}/{$count} activities generated (1 API call) ===");

        return $successCount > 0 ? self::SUCCESS : self::FAILURE;
    }

    private function buildInput(string $type, string $theme, array $config): array
    {
        $input = [];

        $argField = $config['argument'];
        $input[$argField] = $theme;
        $input['theme'] = $theme;

        $input['child'] = $this->option('child') ?: null;
        $input['pages'] = (int) ($this->option('pages') ?: $config['default_pages']);
        $input['agama'] = $this->option('agama') ?: null;

        if ($this->option('ages')) {
            $input['ages'] = $this->parseAges($this->option('ages'));
        }

        if ($this->option('grades')) {
            $input['grades'] = $this->parseGrades($this->option('grades'));
        }

        if ($this->option('subtopic')) {
            $input['subtopic'] = $this->option('subtopic');
        }

        if ($this->option('style')) {
            $input['style'] = $this->option('style');
        }

        if ($this->option('pilar')) {
            $input['pilar'] = $this->option('pilar');
        }

        return $input;
    }

    private function parseAges(string $input): array
    {
        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        $age = (int) $input;
        return range(max(1, $age - 1), min(10, $age + 3));
    }

    private function parseGrades(string $input): array
    {
        if (str_contains($input, ',')) {
            return array_map('intval', array_filter(explode(',', $input), fn($v) => is_numeric($v)));
        }

        return [(int) $input];
    }

    private function generateTheme(AiService $ai, string $type, array $config, ?string $pilarKey): ?string
    {
        $pilars = Pilar::where('pilar_active', true)
            ->orderBy('pilar_sort_order')
            ->get();

        if ($pilars->isEmpty()) {
            $this->error("No active pilars found.");
            return null;
        }

        $pilarLines = [];
        foreach ($pilars as $p) {
            $sub = $p->pilar_subtitle ? " — {$p->pilar_subtitle}" : '';
            $pilarLines[] = "{$p->pilar_emoji} {$p->pilar_title} (key: {$p->pilar_key}){$sub}";
        }
        $pilarsContext = implode("\n", $pilarLines);

        $selectedPilar = null;
        if ($pilarKey) {
            $selectedPilar = $pilars->firstWhere('pilar_key', $pilarKey);
            if (!$selectedPilar) {
                $this->error("Pilar not found: {$pilarKey}");
                $this->line("Available pilars:");
                foreach ($pilars as $p) {
                    $this->line("  <comment>{$p->pilar_key}</comment> — {$p->pilar_emoji} {$p->pilar_title}");
                }
                return null;
            }
        } else {
            $selectedPilar = $pilars->random();
        }

        $label = $config['label'];
        $argLabel = $config['argument_label'];
        $argHint = $config['argument_hint'];

        $ages = $this->option('ages') ? $this->parseAges($this->option('ages')) : [3, 4, 5, 6, 7, 8];
        $minAge = min($ages);
        $maxAge = max($ages);

        $subLine = $selectedPilar->pilar_subtitle ? "Sub: {$selectedPilar->pilar_subtitle}" : "";

        $systemPrompt = <<<PROMPT
Kamu adalah generator ide aktivitas anak Indonesia.

TUGAS: Buat SATU ide tema/{$argLabel} untuk aktivitas "{$label}" yang berkaitan dengan pilar "{$selectedPilar->pilar_title}".

PILAR YANG TERSEDIA:
{$pilarsContext}

PILAR YANG DIPILIH: {$selectedPilar->pilar_emoji} {$selectedPilar->pilar_title} (key: {$selectedPilar->pilar_key})
{$subLine}

ATURAN:
- Tema harus relevan dengan pilar yang dipilih
- Target usia: {$minAge}-{$maxAge} tahun
- Gunakan Bahasa Indonesia sederhana
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- JANGAN gunakan nama tempat
- Ide harus GLOBAL, fokus pada fakta/pengetahuan
- Format: "Hewan/Objek | Fakta spesifik" atau tema sederhana
- Contoh hint: {$argHint}

OUTPUT: HANYA teks tema, tanpa penjelasan lain. Maksimal 50 karakter.
PROMPT;

        $userPrompt = "Buatkan 1 ide {$argLabel} untuk {$label} tentang pilar \"{$selectedPilar->pilar_title}\" untuk anak usia {$minAge}-{$maxAge} tahun.";

        try {
            $provider = config('ai.default_provider') ?? '';
            $model = $ai->getModel($provider);
            $response = $ai->client($provider)->post('/chat/completions', [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            if (!$response->successful()) {
                $this->error("AI request failed: " . $response->status());
                return null;
            }

            $content = trim($response->json()['choices'][0]['message']['content'] ?? '');
            return $content ?: null;
        } catch (\Throwable $e) {
            $this->error("AI error: " . $e->getMessage());
            return null;
        }
    }
}
