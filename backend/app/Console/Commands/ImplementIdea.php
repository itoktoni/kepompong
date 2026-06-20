<?php

namespace App\Console\Commands;

use App\Console\Concerns\UsesAiProvider;
use App\Models\Idea;
use App\Services\ActivityGeneratorService;
use Illuminate\Console\Command;

class ImplementIdea extends Command
{
    use UsesAiProvider;

    protected $signature = 'implement:idea
        {id : Idea ID from database}
        {--count= : Number of activity variations (default: idea_qty from DB)}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model}';

    protected $description = 'Take an idea from DB and generate multiple activity variations using AI';

    public function handle(ActivityGeneratorService $service): int
    {
        $id = $this->argument('id');
        $idea = Idea::find($id);

        if (!$idea) {
            $this->error("Idea #{$id} not found.");
            return self::FAILURE;
        }

        $count = (int) ($this->option('count') ?: $idea->idea_qty ?: 10);

        if (!$idea->idea_type) {
            $this->error("Idea #{$id} has no type. Set idea_type first.");
            return self::FAILURE;
        }

        $type = $idea->idea_type;
        $config = config("activity.types.{$type}");

        if (!$config) {
            $this->error("Unknown type: {$type}");
            return self::FAILURE;
        }

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;

        $this->info("=== Implementing Idea #{$id} ===");
        $this->line("Type    : {$type}");
        $this->line("Theme   : {$idea->idea_nama}");
        $this->line("Count   : {$count}");
        $this->line("AI      : {$provider} / {$model}");
        $this->newLine();

        $this->line("Generating {$count} variations...");
        $variations = $this->generateVariations($ai, $provider, $model, $idea, $count);

        if (empty($variations)) {
            $this->error("Failed to generate variations.");
            return self::FAILURE;
        }

        $this->info("Got " . count($variations) . " variations. Generating activities...");
        $this->newLine();

        $saved = 0;
        $bar = $this->output->createProgressBar(count($variations));
        $bar->start();

        foreach ($variations as $var) {
            $input = [
                'theme'    => $var['title'],
                'topic'    => $var['title'],
                'child'    => 'Anak',
                'pages'    => $config['default_pages'] ?? 16,
                'ages'     => $idea->idea_ages ?? [],
                'agama'    => !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
            ];

            try {
                $result = $service->generateContent($type, $input);
                $service->createActivity($type, $result, $input);
                $saved++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  Failed: {$var['title']} — {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $idea->update([
            'idea_tanggal'     => now()->format('Y-m-d H:i:s'),
            'idea_implementor' => "{$provider}/{$model}",
        ]);

        $this->info("✓ Done! Saved {$saved} activities from idea #{$id}");

        return self::SUCCESS;
    }

    private function generateVariations($ai, string $provider, string $model, Idea $idea, int $count): array
    {
        $theme = $idea->idea_nama;
        $desc = $idea->idea_keterangan ?? '';
        $moral = $idea->idea_moral ?? '';
        $ageRange = !empty($idea->idea_ages) ? min($idea->idea_ages) . '-' . max($idea->idea_ages) : '3-8';

        $systemPrompt = 'Kamu adalah generator konten kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. Output harus dalam format JSON array.';

        $userPrompt = <<<PROMPT
Berdasarkan ide utama ini:
- Topik: {$theme}
- Deskripsi: {$desc}
- Moral: {$moral}

Buatlah {$count} variasi judul aktivitas anak yang berbeda-beda, masing-masing berdasarkan tema yang sama tapi dengan sudut pandang, karakter, atau lokasi yang berbeda.

Contoh variasi dari "Belut Laut > Sungai Mangrove":
- "Unagi si belut lezat yang bisa dimakan di Jepang yang mempunyai kandungan gizi terbaik"
- "Belut listrik di Sungai Amazon yang bisa menghasilkan listrik untuk menerangi gua"
- "Belut Moray di Terumbu Karang yang suka bersembunyi di celah-celah batu"
- "Belut Putih di Danau Michigan yang hampir punah karena polusi air"
- "Sidat Raksasa di Sungai Mekong yang panjangnya bisa mencapai 3 meter"

Setiap variasi harus:
- Unik dan menarik untuk anak usia {$ageRange} tahun
- Menggunakan konteks Indonesia atau dunia yang menarik
- Punya fakta spesifik yang bisa jadi bahan konten
- Punya moral/pelajaran

Output dalam format JSON array:
[
  {
    "title": "Judul variasi yang menarik",
    "desc": "Deskripsi singkat (2-3 kalimat)",
    "moral": "Pelajaran moral"
  }
]

Hanya output JSON. Semua teks harus bahasa Indonesia.
PROMPT;

        try {
            $items = $ai->chat($provider, $model, $systemPrompt, $userPrompt);
            return is_array($items) ? array_filter($items, fn ($item) => isset($item['title'])) : [];
        } catch (\Throwable $e) {
            $this->error("Variation generation failed: {$e->getMessage()}");
            return [];
        }
    }
}
