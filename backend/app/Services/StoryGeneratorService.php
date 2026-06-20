<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoryGeneratorService
{
    private array $templates = [
        'kebersamaan' => [
            'title' => 'Petualangan {nama} di Hutan',
            'pages' => [
                '{nama} adalah anak yang baik hati dan suka bermain di hutan.',
                'Suatu hari, {nama} bertemu seekor kelinci yang kehilangan jalan pulang.',
                '{nama} membantu kelinci itu dengan baik hati.',
                'Kelinci senang dan mengajak {nama} bertemu teman-teman lain di hutan.',
                'Akhirnya {nama} pulang dengan senyuman dan cerita baru.',
            ],
            'moral' => 'Kebaikan hati membuatmu memiliki banyak teman.',
        ],
        'kejujuran' => [
            'title' => 'Kisah {nama} dan Emas',
            'pages' => [
                '{nama} menemukan dompet di jalan.',
                'Di dalamnya ada uang dan kartu identitas.',
                '{nama} memutuskan untuk mengembalikannya ke pemiliknya.',
                'Pemiliknya sangat berterima kasih.',
                '{nama} merasa lega dan sangat bahagia.',
            ],
            'moral' => 'Kejujuran adalah sifat yang mulia.',
        ],
        'kemandirian' => [
            'title' => '{nama} Belajar Berjajan',
            'pages' => [
                '{nama} biasanya dibantu Ibu untuk segalanya.',
                'Hari ini Ibu berkata, "Coba lakukan sendiri ya, Nak."',
                '{nama} mencurahkan susu ke dalam gelas dengan hati-hati.',
                'Meskipok sedikit tumpah, {nama} tetap tersenyum.',
                '{nama} belajar bahwa ia bisa melakukan banyak hal sendiri.',
            ],
            'moral' => 'Belajar mandiri membuatmu semakin percaya diri.',
        ],
        'pergaulan' => [
            'title' => 'Pertemanan Baru {nama}',
            'pages' => [
                '{nama} pindah ke sekolah baru dan merasa canggung.',
                'Di kelas ada anak lain yang juga duduk sendiri.',
                '{nama} memulai percakapan dengan salam tulus.',
                'Mereka akhirnya bermain bersama saat istirahat.',
                '{nama} punya teman baru yang juga menyukai cerita.',
            ],
            'moral' => 'Senyuman dan salam adalah cara mudah mendapatkan teman.',
        ],
    ];

    public function generate(string $theme, string $childName = 'Anak'): array
    {
        $theme = strtolower(trim($theme));
        $childName = trim($childName) ?: 'Anak';

        $template = $this->templates[$theme] ?? $this->buildFallback($theme);

        $title = str_replace('{nama}', $childName, $template['title']);

        $pages = [];
        $num = 1;
        foreach ($template['pages'] as $text) {
            $text = str_replace('{nama}', $childName, $text);
            $pages[] = [
                'num' => $num++,
                'text' => $text,
            ];
        }

        $moral = str_replace('{nama}', $childName, $template['moral']);

        return [
            'title' => $title,
            'moral' => $moral,
            'pages' => $pages,
        ];
    }

    private function buildFallback(string $theme): array
    {
        $title = 'Kisah {nama} tentang ' . ucfirst($theme);
        return [
            'title' => $title,
            'pages' => [
                '{nama} mendengar cerita tentang ' . $theme . '.',
                '{nama} ingin mencobanya hari itu juga.',
                'Langkah demi langkah, {nama} mulai meyakini bahwa bisa.',
                'Akhirnya {nama} memahami nilai dari ' . $theme . '.',
                '{nama} pulang dengan hati yang terasa lebih ringan.',
            ],
            'moral' => 'Mengenal ' . $theme . ' membuat hati semakin hangat.',
        ];
    }

    public function generateWithAI(string $theme, string $childName = 'Anak', int $pagesCount = 24, ?string $locale = null, array $ages = []): array
    {
        $client = $this->openAiClient();

        $locale = $locale ?: strtolower(trim($theme));
        $pagesCount = max(1, min(24, $pagesCount));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT simple sentences (3-6 words per page). Use basic vocabulary. Focus on colors, animals, family. Each page should be 1 short sentence only. Include 1-2 characters.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short simple sentences (5-10 words per page). Simple story with clear sequence. Each page 1-2 short sentences. Include 2-3 characters with different personalities.",
            default => "Target: older children ages 7-10. Use longer sentences (10-20 words per page). Write a RICH detailed story with MANY scenes and locations. Include at least 3-4 named characters with distinct personalities. Each page describes a DIFFERENT scene/location with detailed action and dialogue. Use vivid descriptions of environments, emotions, and interactions. Each page 2-4 sentences with dialogue.",
        };

        $systemPrompt = "You are a children's story writer.\n";
        $systemPrompt .= "CRITICAL: You MUST write EXACTLY {$pagesCount} pages. Not {$pagesCount} minus 1, not {$pagesCount} plus 1. EXACTLY {$pagesCount} pages in the pages array.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. NEVER use Chinese, Arabic, Japanese, Korean, or any non-Latin characters. No emojis, no special unicode symbols.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "Return ONLY JSON with this structure:\n";
        $systemPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\"},{\"text\":\"...\"},...up to EXACTLY {$pagesCount} items]}\n";
        $systemPrompt .= "- desc: a short 1-2 sentence summary of what the story is about\n";
        $systemPrompt .= "- moral: the moral lesson(s) of the story\n";
        $systemPrompt .= "- Theme: {$theme}\n";
        if ($childName && $childName !== 'Anak') {
            $systemPrompt .= "- Main character name: {$childName}\n";
        } else {
            $systemPrompt .= "- Create your own cartoon characters (animals, fantasy creatures, or children). Do NOT use the name 'Anak'. Use creative character names like ikan hiu, kucing lucu, kelinci putih, etc.\n";
        }
        $systemPrompt .= "- Number of pages: {$pagesCount}\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge} years old\n";
        $systemPrompt .= "- The pages array MUST contain EXACTLY {$pagesCount} items. Count them before returning.\n";
        $systemPrompt .= "- Each page text MUST be MAXIMUM 40 words. Keep it concise and impactful.\n";
        $systemPrompt .= "CRITICAL: This content is for CHILDREN ages {$minAge}-{$maxAge}. You MUST use ONLY safe, kind, positive language. NEVER include any swear words, vulgar language, insults, violence, scary content, or inappropriate words in ANY language (Indonesian, English, etc.). Words like bajingan, sialan, bodoh, tolol, gila, bangsat, kampret, tai, anjing, babi, kontol, memek, ngentot, asu, jancok, and ALL similar words are STRICTLY FORBIDDEN. If a character does something wrong, show consequences in a gentle, educational way. Always promote kindness, empathy, and positive values.\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words that children ages {$minAge}-{$maxAge} can understand. FORBIDDEN words: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable, and ANY other complex/foreign words. Use simple words like: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik.\n";

        try {
            $response = $client->post('/chat/completions', [
                'model' => config('services.openai.model', 'MiniMax-M2.7-highspeed'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => 'Buatkan cerita tentang tema: ' . $theme . ($childName && $childName !== 'Anak' ? ' untuk anak bernama ' . $childName : '')],
                ],
                'temperature' => 0.7,
                'max_tokens' => max(8000, $pagesCount * 400),
            ]);

            if (! $response->successful()) {
                Log::warning('Story AI failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'theme' => $theme,
                    'child_name' => $childName,
                ]);
                return $this->fallback($theme, $childName, $pagesCount);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if (empty($content)) {
                Log::warning('Story AI empty content', [
                    'data' => $data,
                    'theme' => $theme,
                    'child_name' => $childName,
                ]);
                return $this->fallback($theme, $childName, $pagesCount);
            }

            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```+\s*$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['pages'])) {
                Log::warning('Story AI invalid response', [
                    'content' => $content,
                    'theme' => $theme,
                    'child_name' => $childName,
                    'pages_requested' => $pagesCount,
                ]);
                return $this->fallback($theme, $childName, $pagesCount);
            }

            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['pages'])) {
                Log::warning('Story AI invalid response', [
                    'content' => $content,
                    'theme' => $theme,
                    'child_name' => $childName,
                    'pages_requested' => $pagesCount,
                ]);
                return $this->fallback($theme, $childName, $pagesCount);
            }

            $pages = array_slice($parsed['pages'], 0, $pagesCount);
            $renumbered = [];
            foreach ($pages as $index => $page) {
                $renumbered[] = [
                    'num' => $index + 1,
                    'text' => $this->cleanText($page['text'] ?? $page['value'] ?? (is_string($page) ? $page : '')),
                ];
            }

            return [
                'title' => $this->cleanText($parsed['title']),
                'desc' => $this->cleanText($parsed['desc'] ?? ''),
                'moral' => $this->cleanText($parsed['moral'] ?? ''),
                'pages' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            Log::warning('Story AI exception', [
                'message' => $e->getMessage(),
                'theme' => $theme,
                'child_name' => $childName,
            ]);
            return $this->fallback($theme, $childName, $pagesCount);
        }
    }

    private function fallback(string $theme, string $childName, int $pagesCount = 4): array
    {
        $pagesCount = max(1, min(24, $pagesCount));
        $base = $this->generate($theme, $childName);
        $base['pages'] = array_slice($base['pages'], 0, $pagesCount);
        return $base;
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = $this->filterProfanity($text);
        return trim($text);
    }

    private function filterProfanity(string $text): string
    {
        $badWords = [
            'bajingan', 'sialan', 'bodoh', 'tolol', 'gila', 'bangsat', 'kampret',
            'tai', 'anjing', 'babi', 'kontol', 'memek', 'ngentot', 'asu', 'jancok',
            'goblok', 'idiot', 'dungu', 'keparat', 'setan', 'tukang tipu',
            'bego', 'bebal', 'bloon', 'dancok', 'jancok', 'cukimak',
            'fuck', 'shit', 'damn', 'hell', 'ass', 'bitch', 'bastard',
            'stupid', 'idiot', 'dumb', 'moron', 'crap', 'piss',
        ];

        $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';
        $text = preg_replace($pattern, 'baik', $text);

        return $text;
    }

    private function openAiClient(): PendingRequest
    {
        $baseUrl = rtrim((string) config('services.openai.base_url', env('OPENAI_BASE_URL', 'https://api.openai.com')), '/');
        $apiKey = (string) config('services.openai.api_key', env('OPENAI_API_KEY', ''));

        return Http::withToken($apiKey)
            ->asJson()
            ->baseUrl($baseUrl)
            ->timeout(60)
            ->retry(2, 500)
            ->acceptJson();
    }
}
