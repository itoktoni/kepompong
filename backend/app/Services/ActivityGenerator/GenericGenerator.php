<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

abstract class GenericGenerator extends BaseGenerator
{
    abstract protected function type(): string;
    abstract protected function label(): string;
    abstract protected function contentGuide(): string;
    abstract protected function defaultPages(): int;

    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? $input['topic'] ?? '';
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;
        $pages = $input['pages'] ?? $this->defaultPages();

        $systemPrompt = 'Kamu adalah generator konten anak Indonesia. Gunakan HANYA bahasa Indonesia dengan alfabet Latin. JANGAN gunakan bahasa lain. JANGAN gunakan kata-kata sulit/bahasa asing. Gunakan kata sederhana yang dipahami anak ' . $minAge . '-' . $maxAge . ' tahun. Output harus dalam format JSON.';

        $guide = $this->contentGuide();

        $themeInput = $theme ?: 'penting untuk anak';
        $userPrompt = <<<PROMPT
Buatkan konten {$this->label()} untuk anak dengan tema: {$themeInput}

Panduan: {$guide}
Jumlah halaman: {$pages}
Usia: {$minAge}-{$maxAge} tahun

ATURAN PENTING:
- JANGAN gunakan "si" di judul
- JANGAN gunakan nama karakter/persona
- Gunakan konteks Indonesia
- Semua teks harus bahasa Indonesia sederhana

Output dalam format JSON:
{
  "title": "Judul konten",
  "desc": "Deskripsi singkat",
  "moral": "Pelajaran moral",
  "pages": [
    {"num": 1, "text": "Isi halaman 1"},
    {"num": 2, "text": "Isi halaman 2"}
  ]
}

Hanya output JSON. Semua teks harus bahasa Indonesia sederhana.
PROMPT;

        $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

        if (!is_array($result) || empty($result['title'])) {
            return [
                'title' => $theme,
                'desc'  => "Konten {$this->label()} tentang {$theme}",
                'moral' => '',
                'pages' => array_map(fn($i) => ['num' => $i, 'text' => "Halaman {$i} tentang {$theme}"], range(1, $pages)),
            ];
        }

        return $result;
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        foreach ($result['pages'] ?? [] as $index => $page) {
            $pages[] = ['num' => $page['num'] ?? ($index + 1), 'text' => $page['text'] ?? ''];
        }

        return array_merge($this->baseActivityData($this->type(), $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages],
        ]);
    }

    public function assetConfig(): array
    {
        return [
            'mode'          => 'grid',
            'default_pages' => $this->defaultPages(),
            'image_size'    => '2K',
            'style'         => 'Modern pixar 3D cartoon, bright colorful daylight, kid friendly.',
            'extra_rules'   => "- No speech bubbles allowed\n- No written text in panels except cover",
        ];
    }

    public function buildPrompt(array $result, array $input): string
    {
        $pages = $result['pages'] ?? [];
        $count = count($pages);
        $title = $result['title'];
        $desc = $result['desc'] ?? '';
        $moral = $result['moral'] ?? '';
        $grid = $this->gridLabel($count);
        $panel = $count - 1;

        $lines = ["Panel 1 (cover): Title \"{$title}\" centered, kid-friendly illustration."];
        foreach ($pages as $i => $p) {
            $lines[] = "Page {$i}: {$p['text']}";
        }

        $p = "A {$count}-panel page storyboard, single image with a {$grid} panel grid.\n\n";
        $p .= "Title: {$title}\nDescription: {$desc}\nMoral: {$moral}\n\n";
        $p .= "Each panel is an illustration:\n\n";
        $p .= implode("\n", $lines) . "\n\n";
        $p .= "Style: Modern pixar 3D cartoon, bright colorful daylight, kid friendly.\n\n";
        $p .= "Rules:\n- Panel 1 is the cover with title text centered\n";
        $p .= "- Cover title is not too big and not too small\n";
        $p .= "- Page 1-{$panel} is content\n";
        $p .= $this->commonRules();

        return $p;
    }
}
