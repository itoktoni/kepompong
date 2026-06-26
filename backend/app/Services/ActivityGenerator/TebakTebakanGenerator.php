<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;

class TebakTebakanGenerator extends GenericGenerator
{
    protected function type(): string { return 'tebak_tebakan'; }
    protected function label(): string { return 'Tebak-tebakan'; }
    protected function defaultPages(): int { return 8; }

    protected function contentGuide(): string
    {
        return 'Create riddles with clues and interesting answers. Each item contains one riddle question with a hint.';
    }

    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? $input['topic'] ?? '';
        $desc = $input['desc'] ?? '';
        $informasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $ages = $input['ages'] ?? [];
        $agama = $input['agama'] ?? null;
        $count = $input['pages'] ?? $this->defaultPages();
        $variation = $input['variation'] ?? 1;

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $parsed = $this->parseKeterangan($desc);
        $titles = $parsed['titles'];
        $latar = $parsed['latar'];

        $selectedTitle = '';
        if (!empty($titles)) {
            $index = ($variation - 1) % count($titles);
            $selectedTitle = $titles[$index];
        }

        $ideaContext = '';
        if (!empty($selectedTitle)) {
            $ideaContext .= "CONTENT TITLE (you MUST use this exact title):\n\"{$selectedTitle}\"\n\n";
        }
        if (!empty($latar)) {
            $ideaContext .= "SETTING / BACKGROUND:\n{$latar}\n\n";
        }
        if (!empty($informasi)) {
            $ideaContext .= "FACTUAL INFORMATION about \"{$theme}\" (use as content background):\n{$informasi}\n";
        }
        if (!empty($notes)) {
            $ideaContext .= "ADDITIONAL INSTRUCTIONS from user:\n{$notes}\n";
        }
        if (!empty($agama)) {
            $ideaContext .= "Religious context: {$agama}\n";
        }

        $systemPrompt = "You are a content generator for Indonesian children.\n";
        $systemPrompt .= "Use ONLY Indonesian language with Latin alphabet.\n";
        $systemPrompt .= "DO NOT use other languages. DO NOT use difficult/foreign words.\n";
        $systemPrompt .= "Use simple words appropriate for children aged {$minAge}-{$maxAge} years old.\n";
        $systemPrompt .= "Output must be in JSON format.";

        $userPrompt = "Create {$count} tebak-tebakan (riddle quiz) questions for children about \"{$theme}\".\n\n";

        if (!empty($ideaContext)) {
            $userPrompt .= "{$ideaContext}\n";
        }

        $userPrompt .= <<<PROMPT
Each question must have:
- "question": A riddle or guessing question (simple Indonesian for kids)
- "answer": The correct answer
- "hint": A helpful clue to guide the child

Rules:
1. Questions must be fun and age-appropriate for {$minAge}-{$maxAge} year olds
2. Use simple, easy-to-understand Indonesian words
3. Hints should help but not give away the answer directly
4. Each question should be different and interesting
5. DO NOT use 'si' in titles!
6. DO NOT use character names!

Output in JSON format:
{
  "title": "Tebak [topic] title",
  "desc": "Short description",
  "moral": "Moral lesson",
  "questions": [
    {"question": "Question text?", "answer": "Answer", "hint": "Helpful clue"},
    {"question": "Question text?", "answer": "Answer", "hint": "Helpful clue"}
  ]
}

Only output JSON. All text in simple Indonesian.
PROMPT;

        try {
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result) || empty($result['title'])) {
                $fallbackTitle = !empty($selectedTitle) ? $selectedTitle : "Tebak-tebakan tentang {$theme}";
                return [
                    'title' => $fallbackTitle,
                    'desc'  => $desc ?: "Tebak-tebakan tentang {$theme}",
                    'moral' => $informasi,
                    'questions' => array_map(fn($i) => [
                        'question' => "Pertanyaan {$i} tentang {$theme}?",
                        'answer' => "Jawaban {$i}",
                        'hint' => "Petunjuk {$i}",
                    ], range(1, $count)),
                ];
            }

            if (!empty($selectedTitle)) {
                $result['title'] = $selectedTitle;
            }

            return $result;
        } catch (\Throwable $e) {
            $fallbackTitle = !empty($selectedTitle) ? $selectedTitle : "Tebak-tebakan tentang {$theme}";
            return [
                'title' => $fallbackTitle,
                'desc'  => $desc ?: "Tebak-tebakan tentang {$theme}",
                'moral' => $informasi,
                'questions' => array_map(fn($i) => [
                    'question' => "Pertanyaan {$i} tentang {$theme}?",
                    'answer' => "Jawaban {$i}",
                    'hint' => "Petunjuk {$i}",
                ], range(1, $count)),
            ];
        }
    }

    public function buildActivityData(array $result, array $input): array
    {
        $questions = [];
        foreach ($result['questions'] ?? [] as $q) {
            $questions[] = [
                'question' => $q['question'] ?? '',
                'answer'   => $q['answer'] ?? '',
                'hint'     => $q['hint'] ?? '',
            ];
        }

        return array_merge($this->baseActivityData($this->type(), $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['questions' => $questions, 'moral' => $result['moral'] ?? ''],
        ]);
    }
}
