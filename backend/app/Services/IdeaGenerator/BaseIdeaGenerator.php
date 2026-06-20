<?php

namespace App\Services\IdeaGenerator;

use App\Contracts\IdeaGeneratorInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseIdeaGenerator implements IdeaGeneratorInterface
{
    abstract protected function typeName(): string;

    protected function resolveConfig(): array
    {
        $type = $this->typeName();
        $default = config('idea.default', []);
        $override = config("idea.types.{$type}", []);

        return [
            'base_url'     => $override['base_url'] ?? $default['base_url'] ?? config('services.openai.base_url'),
            'api_key'      => $override['api_key'] ?? $default['api_key'] ?? config('services.openai.api_key'),
            'model'        => $override['model'] ?? $default['model'] ?? config('services.openai.model'),
            'temperature'  => $override['temperature'] ?? config('idea.defaults.temperature', 0.8),
            'max_tokens_multiplier' => config('idea.defaults.max_tokens_multiplier', 400),
        ];
    }

    protected function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    protected function fallback(int $count): array
    {
        $base = $this->generate();
        $base['items'] = array_slice($base['items'], 0, $count);
        foreach ($base['items'] as $index => &$item) {
            $item['num'] = $index + 1;
        }
        return $base;
    }

    protected function openAiClient(): PendingRequest
    {
        $cfg = $this->resolveConfig();

        return Http::withToken($cfg['api_key'])
            ->asJson()
            ->baseUrl(rtrim((string) $cfg['base_url'], '/'))
            ->timeout(60)
            ->retry(2, 500)
            ->acceptJson();
    }

    protected function buildAgeGuide(int $maxAge): string
    {
        return match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SIMPLE activities with basic motor skills. Focus on sensory play, simple imitation, and basic coordination.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use simple rules, short duration activities. Focus on basic social skills, colors, shapes, numbers, and simple physical activities.",
            default => "Target: older children ages 7-10. Use more complex rules, strategy elements, and longer duration. Focus on critical thinking, teamwork, and skill development.",
        };
    }

    protected function buildAgamaGuide(?string $agama): string
    {
        return match ($agama) {
            'islam' => "Content must be appropriate for Muslim children. Include Islamic values like kindness, sharing, honesty.",
            'kristen' => "Content must be appropriate for Christian children. Include Christian values like love, kindness, honesty.",
            'katholik' => "Content must be appropriate for Catholic children. Include Catholic values like love, kindness, honesty.",
            'hindu' => "Content must be appropriate for Hindu children. Include Hindu values like dharma, kindness, respect.",
            'budha' => "Content must be appropriate for Buddhist children. Include Buddhist values like compassion, mindfulness, kindness.",
            default => "Content must be culturally appropriate for Indonesian children.",
        };
    }

    protected function aiGenerate(string $systemPrompt, string $userContent, int $count, ?string $theme = null): array
    {
        $cfg = $this->resolveConfig();
        $client = $this->openAiClient();

        $systemPrompt .= "\nCRITICAL RULES:\n";
        $systemPrompt .= "- Do NOT use 'si' in titles (WRONG: 'Raja si Paus', RIGHT: 'Paus Sperma di Laut Banda')\n";
        $systemPrompt .= "- Do NOT use character names like Sari, Tika, Luka, Hani, etc. Ideas must be GLOBAL, not stories with specific characters\n";
        $systemPrompt .= "- Titles must be natural Indonesian, not 'X si Y' format\n";
        $systemPrompt .= "- Use ONLY simple Indonesian words that children ages 1-10 can understand\n";
        $systemPrompt .= "- FORBIDDEN words: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable, and ANY other complex/foreign words\n";
        $systemPrompt .= "- Use simple words like: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik\n";

        if ($theme) {
            $userContent .= "\n\nTheme/spesifik topik: {$theme}";
        }

        try {
            $response = $client->post('/chat/completions', [
                'model' => $cfg['model'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userContent],
                ],
                'temperature' => $cfg['temperature'],
                'max_tokens' => max(4000, $count * $cfg['max_tokens_multiplier']),
            ]);

            if (!$response->successful()) {
                Log::warning('Idea AI failed', ['status' => $response->status(), 'body' => $response->body()]);
                return $this->fallback($count);
            }

            $content = trim($response->json()['choices'][0]['message']['content'] ?? '');
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```+\s*$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (!is_array($parsed) || empty($parsed['title']) || empty($parsed['items'])) {
                Log::warning('Idea AI invalid response', ['content' => $content]);
                return $this->fallback($count);
            }

            $items = array_slice($parsed['items'], 0, $count);
            $cleanedItems = [];
            foreach ($items as $index => $item) {
                $cleanedItems[] = [
                    'num'   => $index + 1,
                    'name'  => $this->cleanText($item['name'] ?? ''),
                    'desc'  => $this->cleanText($item['desc'] ?? ''),
                    'moral' => $this->cleanText($item['moral'] ?? ''),
                ];
            }

            return [
                'title'  => $this->cleanText($parsed['title']),
                'items'  => $cleanedItems,
                'source' => 'ai',
                'prompt' => "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userContent}",
            ];
        } catch (\Throwable $e) {
            Log::warning('Idea AI exception', ['message' => $e->getMessage()]);
            return $this->fallback($count);
        }
    }
}
