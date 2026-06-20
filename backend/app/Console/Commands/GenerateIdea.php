<?php

namespace App\Console\Commands;

use App\Console\Concerns\UsesAiProvider;
use App\Models\Idea;
use Illuminate\Console\Command;

class GenerateIdea extends Command
{
    use UsesAiProvider;

    protected $signature = 'generate:idea
        {themes : Themes/topics comma-separated (e.g. "hewan darat, hewan dilindungi")}
        {--count=50 : Number of ideas to generate}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--skills= : Skills to focus on, comma-separated (e.g. berani_bicara,mengelola_marah)}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model (run ai:provider <provider> to list)}';

    protected $description = 'Generate global ideas from themes using AI';

    public function handle(): int
    {
        $themes = array_map('trim', explode(',', $this->argument('themes')));
        $themes = array_filter($themes);
        $count = (int) ($this->option('count') ?: 50);
        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $skills = $this->option('skills') ? array_map('trim', explode(',', $this->option('skills'))) : [];

        $themeStr = implode(' + ', $themes);

        $this->info("=== Generating Ideas ===");
        $this->line("Themes : {$themeStr}");
        $this->line("Count  : {$count}");
        $this->line("Ages   : " . implode(',', $ages));
        $this->line("Agama  : " . ($agama ?: '-'));
        $this->line("Skills : " . (!empty($skills) ? implode(',', $skills) : '-'));

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;
        $this->newLine();

        $systemPrompt = 'Kamu adalah generator ide pengetahuan umum untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. Output harus dalam format JSON array.';
        $userPrompt = $this->buildPrompt($themes, $count, $ages, $agama, $skills);

        try {
            $items = $ai->chat($provider, $model, $systemPrompt, $userPrompt);
        } catch (\Exception $e) {
            $this->error("AI error: {$e->getMessage()}");
            return self::FAILURE;
        }

        if (!is_array($items) || empty($items)) {
            $this->error("AI returned invalid response. Try --count=5 or simpler themes.");
            return self::FAILURE;
        }

        $items = array_filter($items, fn ($item) => isset($item['topik']) && isset($item['fakta']));
        $items = array_values($items);

        $savedCount = 0;

        foreach ($items as $item) {
            $idea = Idea::create([
                'idea_nama' => $item['topik'],
                'idea_keterangan' => $item['fakta'],
                'idea_moral' => $item['moral'] ?? '',
                'idea_type' => null,
                'idea_creator' => $model,
                'idea_tanggal' => null,
                'idea_agama' => $agama ? [$agama] : [],
                'idea_ages' => $ages,
                'idea_skills' => $skills,
            ]);

            $savedCount++;
            $this->line("  [{$savedCount}] {$item['topik']}");
        }

        $this->newLine();
        $this->info("=== Done ===");
        $this->info("Saved {$savedCount} ideas from themes: {$themeStr}");

        return self::SUCCESS;
    }

    private function buildPrompt(array $themes, int $count, array $ages, ?string $agama, array $skills): string
    {
        $themeList = implode(', ', $themes);
        $ageRange = min($ages) . '-' . max($ages);

        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        return <<<PROMPT
Buatlah {$count} ide pengetahuan umum yang menarik untuk anak-anak usia {$ageRange} tahun, berdasarkan tema: {$themeList}

Format setiap ide: Nama > Tempat > Fakta spesifik

Contoh:
- "Komodo > Pulau Komodo > punya air liur yang berbahaya dan mengandung racun, bakteri di mulutnya bisa membunuh mangsa"
- "Burung Cendrawasih > Papua > jantan menari untuk menarik betina, bulunya berwarna sangat indah tanpa pewarna buatan"
- "Candi Borobudur > Jawa Tengah > dibangun tanpa semen, hanya pasak dan alur, punya panel relief cerita Buddha"
- "Badak Jawa > Ujung Kulon > badak bercula satu, hanya tersisa sekitar 80 ekor di dunia, suka mandi di lumpur"
- "Rafflesia > Hutan Sumatra > bunga terbesar di dunia, berbau busuk seperti bangkai, mekar hanya 5-7 hari"

Gunakan konteks Indonesia (nama tempat, hewan, budaya lokal, sejarah).
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Komodo > Pulau Komodo > punya air liur berbahaya...",
    "fakta": "Detail lengkap fakta (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

Kolom "topik" harus mengikuti format: Nama > Tempat > Fakta singkat. Bukan judul cerita.
Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;
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
}
