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

        $systemPrompt = 'Kamu adalah generator konten kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing seperti: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Gunakan kata sederhana: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output harus dalam format JSON array.';

        $userPrompt = <<<PROMPT
Berdasarkan ide utama ini:
- Topik: {$theme}
- Deskripsi: {$desc}
- Moral: {$moral}

Buatlah {$count} variasi judul aktivitas anak yang berbeda-beda, masing-masing berdasarkan tema yang sama tapi dengan sudut pandang atau lokasi yang berbeda.

ATURAN PENTING:
- JANGAN gunakan "si" di judul (contoh SALAH: "Unagi si Belut", BENAR: "Unagi Belut Lezat di Jepang")
- JANGAN gunakan nama karakter/persona (contoh SALAH: "Sari si Paus", BENAR: "Paus Sperma di Laut Banda")
- Judul harus NATURAL, bukan format "X si Y"
- Variasi harus GLOBAL, fokus pada fakta/pengetahuan, bukan cerita dengan tokoh

Contoh variasi yang BENAR dari "Belut Laut > Sungai Mangrove":
- "Unagi Belut Lezat di Jepang yang Punya Kandungan Gizi Terbaik"
- "Belut Listrik di Sungai Amazon yang Menghasilkan Listrik untuk Menerangi Gua"
- "Belut Moray di Terumbu Karang yang Suka Bersembunyi di Celah Batu"
- "Belut Putih di Danau Michigan yang Hampir Punah karena Polusi Air"
- "Sidat Raksasa di Sungai Mekong yang Panjangnya Mencapai 3 Meter"

Contoh yang SALAH (JANGAN ikuti):
- "Unagi si belut lezat" (ada "si")
- "Sari si Penyanyi Paus" (ada nama karakter)

Setiap variasi harus:
- Unik dan menarik untuk anak usia {$ageRange} tahun
- Menggunakan konteks Indonesia atau dunia yang menarik
- Punya fakta spesifik yang bisa jadi bahan konten
- Punya moral/pelajaran

Output dalam format JSON array:
[
  {
    "title": "Judul variasi natural tanpa 'si'",
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
