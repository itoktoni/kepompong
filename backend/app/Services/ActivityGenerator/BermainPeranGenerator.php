<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class BermainPeranGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? $input['topic'] ?? '';
        $ideaDesc = $input['desc'] ?? '';
        $ideaInformasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $ages = $input['ages'] ?? [];
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;
        $pagesCount = max(1, min(25, $input['pages'] ?? 9));
        $variation = $input['variation'] ?? 1;

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT sentences (3-6 words per dialogue). 1 sentence per dialogue line.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short sentences (5-10 words per dialogue). Simple scenario with clear sequence.",
            $maxAge <= 10 => "Target: older children ages 7-10. Use longer sentences (10-20 words per dialogue). More detailed scenario with many scenes.",
            default => "Target: children ages 7-10. Use longer sentences (10-20 words per dialogue). More detailed scenario with many scenes.",
        };

        $themeInput = $theme ?: 'bermain peran seru';

        $parsed = $this->parseKeterangan($ideaDesc);
        $titles = $parsed['titles'];
        $latar = $parsed['latar'];

        $selectedTitle = '';
        if (!empty($titles)) {
            $index = ($variation - 1) % count($titles);
            $selectedTitle = $this->cleanTitleForChild($titles[$index]);
        }

        $systemPrompt = <<<PROMPT
You are a role-play scenario writer for Indonesian children.

CRITICAL: You MUST create EXACTLY {$pagesCount} pages.
CRITICAL: Use ONLY Indonesian language with Latin alphabet. No non-Latin characters. No emojis.
{$ageGuide}

RULES:
- Scenario must be EASY for children to play
- Use familiar professions/situations: doctor, cook, teacher, police, astronaut, etc
- DO NOT use '|' in titles
- DO NOT use 'si' in titles
- DO NOT use character names/persona (use generic roles like Koki, Pelanggan, Dokter, Pasien)
- TITLE must be ATTRACTIVE and FUN for children, like a children's storybook title
- GOOD titles: 'Dokter Kecil yang Berani', 'Petualangan di Dapur', 'Polisi Cilik Penjaga Keamanan'
- BAD titles: 'Bintang Utara | Pemandu jalan', 'Menyikat Gigi | Fakta Gigi'
- Each page has ONE narrator line and 2-3 dialogue lines
- Each narrator MAX 15 words, each dialogue MAX 15 words
- CRITICAL: Use ONLY simple Indonesian words. FORBIDDEN: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable.

OUTPUT JSON FORMAT:
{
  "title": "Judul Menarik",
  "desc": "Deskripsi singkat",
  "moral": "Pelajaran moral",
  "roles": [
    {"name": "Nama Peran", "emoji": "emoji", "desc": "Deskripsi singkat peran"}
  ],
  "pages": [
    {
      "num": 1,
      "narrator": "Deskripsi adegan dalam 1 kalimat pendek",
      "dialog": [
        {"role": "Nama Peran", "text": "Kalimat dialog"},
        {"role": "Nama Peran", "text": "Kalimat dialog"}
      ]
    }
  ]
}

CRITICAL: "pages" MUST have EXACTLY {$pagesCount} items!
Only output JSON. All text in simple Indonesian.
PROMPT;

        $userPrompt = "Create a role-play scenario for children about \"{$themeInput}\".\n\n";
        if (!empty($selectedTitle)) {
            $userPrompt .= "SCENARIO TITLE (you MUST use this exact title):\n\"{$selectedTitle}\"\n\n";
        }
        if (!empty($latar)) {
            $userPrompt .= "SETTING / BACKGROUND (use as scenario environment):\n{$latar}\n\n";
        }
        if ($ideaInformasi) {
            $userPrompt .= "FACTUAL INFORMATION about \"{$themeInput}\" (use as scenario background):\n{$ideaInformasi}\n\n";
        }
        if (!empty($notes)) {
            $userPrompt .= "ADDITIONAL INSTRUCTIONS from user:\n{$notes}\n";
        }
        $userPrompt .= "Number of pages: {$pagesCount}\n";
        $userPrompt .= "Age: {$minAge}-{$maxAge} years old\n\n";
        $userPrompt .= "IMPORTANT: Each page MUST have 'narrator' (scene description) and 'dialog' (array of {role, text})\n";
        $userPrompt .= "Only output JSON. All text in simple Indonesian.";

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title']) || empty($result['pages'])) {
                return $this->fallback($theme, $ideaDesc, $ideaInformasi, $pagesCount);
            }

            $roles = [];
            if (!empty($result['roles'])) {
                foreach ($result['roles'] as $r) {
                    $roles[] = [
                        'name'  => $this->cleanText($r['name'] ?? ''),
                        'emoji' => $r['emoji'] ?? '',
                        'desc'  => $this->cleanText($r['desc'] ?? ''),
                    ];
                }
            }

            $pages = array_slice($result['pages'], 0, $pagesCount);
            $renumbered = [];
            foreach ($pages as $index => $page) {
                $dialog = [];
                if (!empty($page['dialog'])) {
                    foreach ($page['dialog'] as $d) {
                        $dialog[] = [
                            'role' => $this->cleanText($d['role'] ?? ''),
                            'text' => $this->cleanText($d['text'] ?? ''),
                        ];
                    }
                }
                $renumbered[] = [
                    'num'     => $index + 1,
                    'narrator' => $this->cleanText($page['narrator'] ?? $page['text'] ?? ''),
                    'dialog'  => $dialog,
                ];
            }

            $finalTitle = !empty($selectedTitle) ? $selectedTitle : ($result['title'] ?? $theme);

            return [
                'title'  => $this->cleanText($finalTitle),
                'desc'   => $this->cleanText($result['desc'] ?? $ideaDesc),
                'moral'  => $this->cleanText($result['moral'] ?? $ideaInformasi),
                'roles'  => $roles,
                'pages'  => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            return $this->fallback($theme, $ideaDesc, $ideaInformasi, $pagesCount);
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $roles = [];
        foreach ($result['roles'] ?? [] as $r) {
            $roles[] = [
                'name'  => $r['name'] ?? '',
                'emoji' => $r['emoji'] ?? '',
                'desc'  => $r['desc'] ?? '',
            ];
        }

        $pages = [];
        foreach ($result['pages'] as $index => $page) {
            $dialog = [];
            foreach ($page['dialog'] ?? [] as $d) {
                $dialog[] = ['role' => $d['role'] ?? '', 'text' => $d['text'] ?? ''];
            }
            $pages[] = [
                'num'      => $index + 1,
                'narrator' => $page['narrator'] ?? '',
                'dialog'   => $dialog,
            ];
        }

        return array_merge($this->baseActivityData('bermain_peran', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => [
                'roles' => $roles,
                'pages' => $pages,
            ],
        ]);
    }

    private function fallback(string $theme, string $ideaDesc, string $ideaMoral, int $pagesCount): array
    {
        $title = 'Bermain Peran ' . ucfirst($theme);
        $roles = [
            ['name' => 'Pemain 1', 'emoji' => '👦', 'desc' => 'Peran utama'],
            ['name' => 'Pemain 2', 'emoji' => '👧', 'desc' => 'Peran lawan main'],
        ];
        $pages = [];
        for ($i = 1; $i <= $pagesCount; $i++) {
            $pages[] = [
                'num'      => $i,
                'narrator' => "Adegan {$i} tentang {$theme}",
                'dialog'   => [
                    ['role' => 'Pemain 1', 'text' => "Dialog {$i}a"],
                    ['role' => 'Pemain 2', 'text' => "Dialog {$i}b"],
                ],
            ];
        }
        return ['title' => $title, 'desc' => $ideaDesc, 'moral' => $ideaMoral, 'roles' => $roles, 'pages' => $pages];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
