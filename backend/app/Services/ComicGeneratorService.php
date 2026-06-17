<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ComicGeneratorService
{
    private array $templates = [
        'petualangan' => [
            'title' => 'Petualangan {nama} di Hutan Ajaib',
            'pages' => [
                '{nama} menemukan peta tua di loteng rumahnya.',
                'Peta itu menunjukkan jalan ke hutan ajaib yang belum pernah dikunjungi.',
                '{nama} berjalan melewati pintu gerbang dari pohon-pohon raksasa.',
                'Di dalam hutan, {nama} bertemu seekor kelinci yang bisa bicara.',
                'Kelinci itu mengajak {nama} ke sungai kristal yang berkilau.',
                'Mereka menemukan gua tersembunyi di balik air terjun.',
                'Di dalam gua, ada kristal ajaib yang bersinar terang.',
                '{nama} mengambil kristal dan hutan berubah menjadi lebih indah.',
                'Semua hewan hutan datang merayakan bersama {nama}.',
                '{nama} pulang dengan hati gembira dan cerita yang tak terlupakan.',
            ],
            'moral' => 'Keberanian untuk menjelajah membawa kita pada keajaiban yang tak terduga.',
        ],
        'persahabatan' => [
            'title' => '{nama} dan Sahabat Baru',
            'pages' => [
                '{nama} duduk sendirian di taman sekolah.',
                'Anak baru bernama Rina datang dan duduk di sebelahnya.',
                '{nama} menawarkan bekalnya kepada Rina.',
                'Mereka bermain ayunan bersama sambil tertawa.',
                'Rina mengajak {nama} ke rumahnya untuk bermain.',
                'Mereka menggambar bersama di kamar Rina.',
                'Rina menghadiahkan gambar untuk {nama}.',
                '{nama} mengajak Rina bermain layang-layang di lapangan.',
                'Mereka berdua menjadi teman yang tidak terpisahkan.',
                '{nama} belajar bahwa persahabatan dimulai dari satu sapaan.',
            ],
            'moral' => 'Satu sapaan tulus bisa memulai persahabatan yang indah.',
        ],
        'kejujuran' => [
            'title' => '{nama} dan Kejujuran',
            'pages' => [
                '{nama} menemukan dompet di jalan.',
                'Di dalamnya ada uang dan kartu identitas.',
                '{nama} tergoda ingin menyimpan uangnya.',
                'Ibu mengajarkan pentingnya kejujuran.',
                '{nama} memutuskan untuk mengembalikan dompet.',
                '{nama} menemukan alamat pemilik dompet.',
                '{nama} datang ke rumah pemilik dompet.',
                'Pemilik dompet sangat berterima kasih.',
                'Pemilik dompet memberi hadiah untuk {nama}.',
                '{nama} merasa lega dan bahagia karena jujur.',
            ],
            'moral' => 'Kejujuran adalah sifat mulia yang membawa kebahagiaan.',
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
        $title = '{nama} dan Kisah ' . ucfirst($theme);
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

    public function generateWithAI(string $theme, string $childName = 'Anak', int $panelsCount = 16, ?string $locale = null, array $ages = []): array
    {
        $client = $this->openAiClient();

        $locale = $locale ?: strtolower(trim($theme));
        $panelsCount = max(4, min(25, $panelsCount));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SHORT simple sentences (3-6 words per panel). Use basic vocabulary. Focus on colors, animals, family. Each panel should be 1 short sentence only. Include 1-2 characters.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use short simple sentences (5-10 words per panel). Simple story with clear sequence. Each panel 1-2 short sentences. Include 2-3 characters with different personalities.",
            default => "Target: older children ages 7-10. Use longer sentences (10-20 words per panel). Write a RICH detailed story with MANY scenes and locations. Include at least 3-4 named characters with distinct personalities. Each panel describes a DIFFERENT scene/location with detailed action and dialogue. Use vivid descriptions of environments, emotions, and interactions. Each panel 2-4 sentences with dialogue.",
        };

        $systemPrompt = "You are a children's comic book writer.\n";
        $systemPrompt .= "CRITICAL: You MUST write EXACTLY {$panelsCount} panels. Not {$panelsCount} minus 1, not {$panelsCount} plus 1. EXACTLY {$panelsCount} panels in the pages array.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. NEVER use Chinese, Arabic, Japanese, Korean, or any non-Latin characters. No emojis, no special unicode symbols.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "IMPORTANT COMIC FEATURES:\n";
        $systemPrompt .= "- Each panel MUST have a 'dialogue' field with short speech bubble text (MAX 10 words).\n";
        $systemPrompt .= "- Dialogue should be like a conversation: short, punchy, kid-friendly.\n";
        $systemPrompt .= "- Not every panel needs dialogue, but most should have it.\n";
        $systemPrompt .= "- Dialogue examples: 'Hai!', 'Ayo bermain!', 'Terima kasih!', 'Itu lucu!', 'Aduh!', 'Wow!'\n";
        $systemPrompt .= "- Each panel can be split into 2 parts for sharing (left half and right half of panel).\n";
        $systemPrompt .= "Return ONLY JSON with this structure:\n";
        $systemPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"moral\":\"...\",\"pages\":[{\"text\":\"...\",\"dialogue\":\"...\"},{\"text\":\"...\",\"dialogue\":\"...\"},...up to EXACTLY {$panelsCount} items]}\n";
        $systemPrompt .= "- desc: a short 1-2 sentence summary of what the comic is about\n";
        $systemPrompt .= "- moral: the moral lesson(s) of the comic\n";
        $systemPrompt .= "- Theme: {$theme}\n";
        if ($childName && $childName !== 'Anak') {
            $systemPrompt .= "- Main character name: {$childName}\n";
        } else {
            $systemPrompt .= "- Create your own cartoon characters (animals, fantasy creatures, or children). Do NOT use the name 'Anak'. Use creative character names like kucing lucu, kelinci putih, dinosaurus kecil, etc.\n";
        }
        $systemPrompt .= "- Number of panels: {$panelsCount}\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge} years old\n";
        $systemPrompt .= "- The pages array MUST contain EXACTLY {$panelsCount} items. Count them before returning.\n";
        $systemPrompt .= "- Each panel text MUST be MAXIMUM 40 words. Keep it concise and impactful.\n";
        $systemPrompt .= "- This is a COMIC format, so each panel should describe a visual scene with action and dialogue.\n";
        $systemPrompt .= "- Each panel can be divided into 2 parts (left/right halves) for social media sharing.\n";
        $systemPrompt .= "CRITICAL: This content is for CHILDREN ages {$minAge}-{$maxAge}. You MUST use ONLY safe, kind, positive language. NEVER include any swear words, vulgar language, insults, violence, scary content, or inappropriate words in ANY language (Indonesian, English, etc.). Words like bajingan, sialan, bodoh, tolol, gila, bangsat, kampret, tai, anjing, babi, kontol, memek, ngentot, asu, jancok, and ALL similar words are STRICTLY FORBIDDEN. If a character does something wrong, show consequences in a gentle, educational way. Always promote kindness, empathy, and positive values.\n";

        try {
            $response = $client->post('/chat/completions', [
                'model' => config('services.openai.model', 'MiniMax-M2.7-highspeed'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => 'Buatkan komik anak tentang tema: ' . $theme . ($childName && $childName !== 'Anak' ? ' untuk anak bernama ' . $childName : '')],
                ],
                'temperature' => 0.7,
                'max_tokens' => max(8000, $panelsCount * 400),
            ]);

            if (! $response->successful()) {
                Log::warning('Comic AI failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'theme' => $theme,
                    'child_name' => $childName,
                ]);
                return $this->fallback($theme, $childName, $panelsCount);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if (empty($content)) {
                Log::warning('Comic AI empty content', [
                    'data' => $data,
                    'theme' => $theme,
                    'child_name' => $childName,
                ]);
                return $this->fallback($theme, $childName, $panelsCount);
            }

            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```+\s*$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['pages'])) {
                Log::warning('Comic AI invalid response', [
                    'content' => $content,
                    'theme' => $theme,
                    'child_name' => $childName,
                    'panels_requested' => $panelsCount,
                ]);
                return $this->fallback($theme, $childName, $panelsCount);
            }

            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['pages'])) {
                Log::warning('Comic AI invalid response', [
                    'content' => $content,
                    'theme' => $theme,
                    'child_name' => $childName,
                    'panels_requested' => $panelsCount,
                ]);
                return $this->fallback($theme, $childName, $panelsCount);
            }

            $pages = array_slice($parsed['pages'], 0, $panelsCount);
            $renumbered = [];
            foreach ($pages as $index => $page) {
                $renumbered[] = [
                    'num' => $index + 1,
                    'text' => $this->cleanText($page['text'] ?? $page['value'] ?? (is_string($page) ? $page : '')),
                    'dialogue' => $this->cleanText($page['dialogue'] ?? ''),
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
            Log::warning('Comic AI exception', [
                'message' => $e->getMessage(),
                'theme' => $theme,
                'child_name' => $childName,
            ]);
            return $this->fallback($theme, $childName, $panelsCount);
        }
    }

    private function fallback(string $theme, string $childName, int $panelsCount = 10): array
    {
        $panelsCount = max(4, min(25, $panelsCount));
        $base = $this->generate($theme, $childName);
        $base['pages'] = array_slice($base['pages'], 0, $panelsCount);
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
