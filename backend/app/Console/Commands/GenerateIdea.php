<?php

namespace App\Console\Commands;

use App\Console\Concerns\UsesAiProvider;
use App\ActivityType;
use App\Models\Idea;
use Illuminate\Console\Command;

class GenerateIdea extends Command
{
    use UsesAiProvider;

    protected $signature = 'generate:idea
        {themes : Themes/topics comma-separated (e.g. "hewan darat, hewan dilindungi")}
        {type : Activity type (storytelling, komik, puzzle, etc)}
        {--count=10 : Number of ideas to generate}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--skills= : Skills to focus on, comma-separated (e.g. berani_bicara,mengelola_marah)}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model (run ai:provider <provider> to list)}';

    protected $description = 'Generate ideas from themes for a specific activity type using AI';

    public function handle(): int
    {
        $themes = array_map('trim', explode(',', $this->argument('themes')));
        $themes = array_filter($themes);
        $type = $this->argument('type');
        $count = (int) ($this->option('count') ?: 10);
        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $skills = $this->option('skills') ? array_map('trim', explode(',', $this->option('skills'))) : [];

        if (!ActivityType::tryFrom($type)) {
            $this->error("Unknown type: {$type}");
            $this->line("Available types:");
            foreach (ActivityType::cases() as $case) {
                $this->line("  <comment>{$case->value}</comment> — {$case->emoji()} {$case->description()}");
            }
            return self::FAILURE;
        }

        $themeStr = implode(' + ', $themes);

        $this->info("=== Generating Ideas ===");
        $this->line("Themes : {$themeStr}");
        $this->line("Type   : {$type}");
        $this->line("Count  : {$count}");
        $this->line("Ages   : " . implode(',', $ages));
        $this->line("Agama  : " . ($agama ?: '-'));
        $this->line("Skills : " . (!empty($skills) ? implode(',', $skills) : '-'));

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;
        $this->newLine();

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing seperti: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable. Gunakan kata sederhana: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik. Output harus dalam format JSON array.';
        $userPrompt = $this->buildTypePrompt($themes, $type, $count, $ages, $agama, $skills);

        $fullPrompt = "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userPrompt}";

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
            Idea::create([
                'idea_nama' => $item['topik'],
                'idea_keterangan' => $item['fakta'],
                'idea_moral' => $item['moral'] ?? '',
                'idea_type' => $type,
                'idea_creator' => $model,
                'idea_tanggal' => null,
                'idea_agama' => $agama ? [$agama] : [],
                'idea_ages' => $ages,
                'idea_skills' => $skills,
                'idea_qty' => $count,
                'idea_prompt' => $fullPrompt,
            ]);

            $savedCount++;
            $this->line("  [{$savedCount}] {$item['topik']}");
        }

        $this->newLine();
        $this->info("=== Done ===");
        $this->info("Saved {$savedCount} ideas from themes: {$themeStr}" . ($type ? " (type: {$type})" : ''));

        return self::SUCCESS;
    }

    private function buildTypePrompt(array $themes, string $type, int $count, array $ages, ?string $agama, array $skills): string
    {
        $themeList = implode(', ', $themes);
        $ageRange = min($ages) . '-' . max($ages);
        $skillLine = !empty($skills) ? "\nFokus skill: " . implode(', ', $skills) : '';
        $agamaLine = $agama ? "\nAgama: {$agama}" : '';

        $typeGuide = match ($type) {
            'storytelling' => 'Buat ide cerita dengan karakter utama, konflik, dan penyelesaian. Setiap ide harus punya nama karakter, latar tempat, dan pesan moral.',
            'bermain_peran' => 'Buat ide skenario bermain peran dengan peran dan situasi yang menarik untuk anak.',
            'permainan' => 'Buat ide permainan seru dengan aturan sederhana yang bisa dimainkan anak.',
            'monolog' => 'Buat ide naskah monolog dengan karakter dan tema yang relate dengan anak.',
            'proyek_kreatif' => 'Buat ide proyek seni dan kerajinan dengan bahan yang mudah didapat.',
            'musik_gerak' => 'Buat ide lagu dan gerakan untuk anak dengan lirik sederhana.',
            'puzzle' => 'Buat ide puzzle dan teka-teki yang melatih logika anak.',
            'mindfulness' => 'Buat ide latihan mindfulness dan refleksi untuk anak.',
            'outdoor' => 'Buat ide eksplorasi outdoor dan petualangan alam untuk anak.',
            'ilmu_pengetahuan' => 'Buat ide eksperimen sains sederhana untuk anak.',
            'tebak_teakan' => 'Buat ide tebak-tebakan dengan clue dan jawaban yang menarik.',
            'permainan_tangan' => 'Buat ide permainan jari dan tangan untuk anak.',
            'latihan_otak' => 'Buat ide latihan otak dan brain training untuk anak.',
            'komik' => 'Buat ide komik dengan karakter, dialog, dan panel yang menarik.',
            'worksheet' => 'Buat ide worksheet edukatif dengan soal dan aktivitas yang menarik.',
            'coloring' => 'Buat ide halaman mewarnai dengan gambar yang menarik untuk anak.',
            default => 'Buat ide konten kreatif untuk anak.',
        };

        return <<<PROMPT
Buatlah {$count} ide untuk konten bertipe "{$type}" ({$typeGuide}), berdasarkan tema: {$themeList}

Ide harus berupa fakta/pengetahuan spesifik yang bisa dijadikan bahan konten {$type}.

ATURAN PENTING:
- JANGAN gunakan "si" di judul (contoh SALAH: "Raja si Paus", BENAR: "Paus Sperma di Laut Banda")
- JANGAN gunakan nama karakter/persona (contoh SALAH: "Sari si Paus", BENAR: "Paus Sperma di Laut Banda")
- Ide harus GLOBAL, bukan cerita spesifik dengan tokoh
- Format: Hewan/Objek > Tempat > Fakta spesifik

Contoh yang BENAR:
- "Paus Sperma > Laut Banda > bisa menyelam hingga 3 kilometer untuk mencari makanan di kedalaman laut"
- "Ikan Mola-mola > Nusa Penida > ikan terberat di dunia yang bisa mencapai 2 ton, suka berjemur di permukaan laut"
- "Pari Manta > Raja Ampat > bisa terbang melompat keluar air, sayapnya bisa mencapai 7 meter"

Contoh yang SALAH (JANGAN ikuti):
- "Raja si Paus Sperma yang Bisa Menyelam" (ada "si")
- "Sari si Penyanyi Paus" (ada nama karakter)

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Hewan/Objek > Tempat > Fakta singkat",
    "fakta": "Detail lengkap fakta (3-5 kalimat spesifik)",
    "moral": "Pelajaran yang bisa diambil"
  }
]

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
