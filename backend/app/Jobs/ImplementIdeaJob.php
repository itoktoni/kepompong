<?php

namespace App\Jobs;

use App\Models\Idea;
use App\Services\ActivityGeneratorService;
use App\Services\AiService;
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
        public ?int $count = null,
    ) {}

    public function handle(ActivityGeneratorService $service, AiService $ai): void
    {
        $idea = Idea::findOrFail($this->ideaId);
        $count = $this->count ?? $idea->idea_qty ?? 10;

        Log::info('ImplementIdeaJob started', ['idea_id' => $this->ideaId, 'count' => $count]);

        try {
            $idea = Idea::findOrFail($this->ideaId);

            if (!$idea->idea_type) {
                Log::warning('ImplementIdeaJob: idea has no type', ['idea_id' => $this->ideaId]);
                return;
            }

            $type = $idea->idea_type;
            $config = config("activity.types.{$type}");

            if (!$config) {
                Log::warning('ImplementIdeaJob: unknown type', ['type' => $type]);
                return;
            }

            $provider = config('ai.default_provider');
            $model = $ai->getModel($provider);

            $variations = $this->generateVariations($ai, $provider, $model, $idea, $count);

            if (empty($variations)) {
                Log::warning('ImplementIdeaJob: no variations generated');
                return;
            }

            $saved = 0;
            foreach ($variations as $i => $var) {
                Log::info("ImplementIdeaJob generating activity {$i}", ['title' => $var['title']]);

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
                    Log::error("ImplementIdeaJob activity failed", [
                        'title' => $var['title'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $idea->update([
                'idea_tanggal'     => now()->format('Y-m-d H:i:s'),
                'idea_implementor' => "{$provider}/{$model}",
            ]);

            Log::info('ImplementIdeaJob completed', [
                'idea_id'  => $this->ideaId,
                'saved'    => $saved,
                'total'    => count($variations),
            ]);

        } catch (\Throwable $e) {
            Log::error('ImplementIdeaJob failed', [
                'idea_id' => $this->ideaId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function generateVariations(AiService $ai, string $provider, string $model, Idea $idea, int $count): array
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

            if (!is_array($items)) {
                return [];
            }

            return array_filter($items, fn ($item) => isset($item['title']));
        } catch (\Throwable $e) {
            Log::error('ImplementIdeaJob: variation generation failed', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
