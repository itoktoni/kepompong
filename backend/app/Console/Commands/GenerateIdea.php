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
        {--type= : Activity type (storytelling, komik, puzzle, etc). If set, ideas are tailored for that type}
        {--count=10 : Number of ideas to generate}
        {--ages= : Target ages, e.g. 7 means [6,7,8,9,10] or comma-separated 3,4,5,6,7,8}
        {--agama= : Religion tag (e.g. islam, kristen, katholik, hindu, budha)}
        {--skills= : Skills to focus on, comma-separated (e.g. berani_bicara,mengelola_marah)}
        {--provider= : AI provider (run ai:provider to list)}
        {--model= : AI model (run ai:provider <provider> to list)}';

    protected $description = 'Generate ideas from themes using AI. Use --type to tailor for specific activity type.';

    public function handle(): int
    {
        $themes = array_map('trim', explode(',', $this->argument('themes')));
        $themes = array_filter($themes);
        $type = $this->option('type');
        $count = (int) ($this->option('count') ?: 10);
        $ages = $this->parseAges($this->option('ages'));
        $agama = $this->option('agama') ? strtolower(trim($this->option('agama'))) : null;
        $skills = $this->option('skills') ? array_map('trim', explode(',', $this->option('skills'))) : [];

        if ($type && !ActivityType::tryFrom($type)) {
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
        $this->line("Type   : " . ($type ?: 'general (no type)'));
        $this->line("Count  : {$count}");
        $this->line("Ages   : " . implode(',', $ages));
        $this->line("Agama  : " . ($agama ?: '-'));
        $this->line("Skills : " . (!empty($skills) ? implode(',', $skills) : '-'));

        [$ai, $provider, $model] = $this->resolveAi();
        if (!$ai) return self::FAILURE;
        $this->newLine();

        $systemPrompt = 'Kamu adalah generator ide kreatif untuk anak-anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. Output harus dalam format JSON array.';
        $userPrompt = $type
            ? $this->buildTypePrompt($themes, $type, $count, $ages, $agama, $skills)
            : $this->buildGeneralPrompt($themes, $count, $ages, $agama, $skills);

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
            ]);

            $savedCount++;
            $this->line("  [{$savedCount}] {$item['topik']}");
        }

        $this->newLine();
        $this->info("=== Done ===");
        $this->info("Saved {$savedCount} ideas from themes: {$themeStr}" . ($type ? " (type: {$type})" : ''));

        return self::SUCCESS;
    }

    private function buildGeneralPrompt(array $themes, int $count, array $ages, ?string $agama, array $skills): string
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

Format: Nama > Tempat > Fakta spesifik

Contoh untuk type "storytelling":
- "Komodo > Pulau Komodo > punya air liur berbahaya, suatu hari seekor komodo muda bernama Komo tersesat dan harus menggunakan indra penciumannya yang tajam untuk pulang"
- "Burung Cendrawasih > Papua > bulunya sangat indah, seekor burung cendrawasih bernama Candra belajar menari untuk pertama kalinya di hutan Papua"

Contoh untuk type "komik":
- "Badak Jawa > Ujung Kulon > hanya tersisa 80 ekor, seekor badak muda bernama Bono petualang mencari teman di hutan yang semakin sempit"

Gunakan konteks Indonesia.
{$skillLine}{$agamaLine}

Output dalam format JSON array:
[
  {
    "topik": "Nama > Tempat > Fakta singkat",
    "fakta": "Detail lengkap yang bisa jadi bahan konten {$type} (3-5 kalimat)",
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
