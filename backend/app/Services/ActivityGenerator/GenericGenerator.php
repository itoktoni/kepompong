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
        $desc = $input['desc'] ?? '';
        $moral = $input['moral'] ?? '';
        $child = $input['child'] ?? 'Anak';
        $ages = $input['ages'] ?? [];
        $agama = $input['agama'] ?? null;
        $pages = $input['pages'] ?? $this->defaultPages();

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        // Build context from idea data
        $ideaContext = '';
        if (!empty($desc)) {
            $ideaContext .= "- Deskripsi: {$desc}\n";
        }
        if (!empty($moral)) {
            $ideaContext .= "- Pelajaran moral: {$moral}\n";
        }
        if (!empty($agama)) {
            $ideaContext .= "- Konteks agama: {$agama}\n";
        }

        $systemPrompt = "You are a content generator for Indonesian children.\n";
        $systemPrompt .= "Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "DO NOT use other languages. DO NOT use difficult/foreign words.\n";
        $systemPrompt .= "Use simple words appropriate for children aged {$minAge}-{$maxAge} years old.\n";
        $systemPrompt .= "TITLE FORMAT: Animal/Object at Location\n";
        $systemPrompt .= "DO NOT use 'si' in titles. DO NOT use character names.\n";
        $systemPrompt .= "Output must be in JSON format.";

        $guide = $this->contentGuide();

        $themeInput = $theme ?: 'important for children';
        $userPrompt = "Generate {$this->label()} content for children with theme: {$themeInput}\n\n";

        if (!empty($ideaContext)) {
            $userPrompt .= "IDEA CONTEXT:\n{$ideaContext}\n\n";
        }

        $userPrompt .= <<<PROMPT
Guide: {$guide}
Number of pages: {$pages}
Age: {$minAge}-{$maxAge} years old

ABSOLUTE RULES - MUST FOLLOW:
1. DO NOT use 'si' in titles at all!
   WRONG: 'Si Paus', 'Pak Si Hiu', 'Dina si Penjelajah'
   CORRECT: 'Petualangan Paus Sperma', 'Kisah Hiu Paus yang Pemalu'
2. DO NOT use character names: Dina, Bono, Luna, Wibi, etc.
3. DO NOT use '>' in titles!
   WRONG: 'Kisah tentang Kuda Laut Kerdil > Dasar Laut Jawa'
   WRONG: 'Ikan Tongkol > Laut Jawa'
   CORRECT: 'Kuda Laut Kerdil di Dasar Laut Jawa'
   CORRECT: 'Petualangan Ikan Tongkol di Laut Jawa'
4. TITLES MUST BE ATTRACTIVE like children's story book titles!
   - Titles like: 'Si Kancil yang Cerdik', 'Kelinci dan Kura-kura', 'Petualangan di Hutan'
   - Can use numbers: '3 Fakta Menarik tentang Hiu'
   - Can use location: 'Kuda Laut Kerdil di Dasar Laut Jawa'
   - NOT format: 'Theme > Location > Explanation'

Output in JSON format:
{
  "title": "Content title",
  "desc": "Short description",
  "moral": "Moral lesson",
  "pages": [
    {"num": 1, "text": "Page 1 content"},
    {"num": 2, "text": "Page 2 content"}
  ]
}

Only output JSON. All text in simple Indonesian.
PROMPT;

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title'])) {
                return [
                    'title' => $theme,
                    'desc'  => $desc ?: "Konten {$this->label()} tentang {$theme}",
                    'moral' => $moral,
                    'pages' => array_map(fn($i) => ['num' => $i, 'text' => "Halaman {$i} tentang {$theme}"], range(1, $pages)),
                ];
            }

            return $result;
        } catch (\Throwable $e) {
            return [
                'title' => $theme,
                'desc'  => $desc ?: "Konten {$this->label()} tentang {$theme}",
                'moral' => $moral,
                'pages' => array_map(fn($i) => ['num' => $i, 'text' => "Halaman {$i} tentang {$theme}"], range(1, $pages)),
            ];
        }
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
