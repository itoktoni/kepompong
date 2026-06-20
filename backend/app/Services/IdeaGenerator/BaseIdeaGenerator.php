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

    protected function aiGenerate(string $systemPrompt, string $userPrompt, int $count): array
    {
        $cfg = $this->resolveConfig();
        $client = $this->openAiClient();

        try {
            $response = $client->post('/chat/completions', [
                'model' => $cfg['model'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
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
            if (!is_array($parsed)) {
                Log::warning('Idea AI invalid JSON', ['content' => $content]);
                return $this->fallback($count);
            }

            $items = array_filter($parsed, fn ($item) => isset($item['topik']) && isset($item['fakta']));
            $items = array_values($items);

            if (empty($items)) {
                Log::warning('Idea AI no valid items', ['content' => $content]);
                return $this->fallback($count);
            }

            $cleanedItems = [];
            foreach (array_slice($items, 0, $count) as $index => $item) {
                $cleanedItems[] = [
                    'num'   => $index + 1,
                    'name'  => $this->cleanText($item['topik'] ?? ''),
                    'desc'  => $this->cleanText($item['fakta'] ?? ''),
                    'moral' => $this->cleanText($item['moral'] ?? ''),
                ];
            }

            $fullPrompt = "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userPrompt}";

            return [
                'title'  => $this->cleanText($parsed['title'] ?? $cleanedItems[0]['name'] ?? ''),
                'items'  => $cleanedItems,
                'source' => 'ai',
                'prompt' => $fullPrompt,
            ];
        } catch (\Throwable $e) {
            Log::warning('Idea AI exception', ['message' => $e->getMessage()]);
            return $this->fallback($count);
        }
    }
}
