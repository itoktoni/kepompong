<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AiService
{
    private array $resolved = [];

    public function getProvider(?string $name = null): array
    {
        $name = $name ?: config('ai.default_provider', 'openai');

        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        $provider = config("ai.providers.{$name}");

        if (!$provider) {
            throw new \InvalidArgumentException("AI provider [{$name}] not found. Available: " . implode(', ', array_keys(config('ai.providers', []))));
        }

        $this->resolved[$name] = $provider;

        return $provider;
    }

    public function getModel(?string $provider = null, ?string $model = null): string
    {
        $p = $this->getProvider($provider);

        if ($model && array_key_exists($model, $p['models'])) {
            return $model;
        }

        $default = config('ai.default_model');

        if ($default && array_key_exists($default, $p['models'])) {
            return $default;
        }

        return array_key_first($p['models']);
    }

    public function getModelConfig(?string $provider = null, ?string $model = null): array
    {
        $p = $this->getProvider($provider);
        $m = $this->getModel($provider, $model);

        return $p['models'][$m] ?? [];
    }

    public function client(?string $provider = null): PendingRequest
    {
        $p = $this->getProvider($provider);

        return Http::withToken($p['api_key'])
            ->asJson()
            ->baseUrl(rtrim((string) $p['base_url'], '/'))
            ->timeout(180)
            ->retry(2, 2000)
            ->acceptJson();
    }

    public function chat(?string $provider, ?string $model, string $systemPrompt, string $userContent, ?float $temperature = null, ?int $maxTokens = null): ?array
    {
        $m = $this->getModel($provider, $model);
        $modelCfg = $this->getModelConfig($provider, $model);
        $temp = $temperature ?? $modelCfg['temperature'] ?? 0.7;

        $response = $this->client($provider)->post('/chat/completions', [
            'model'       => $m,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userContent],
            ],
            'temperature' => $temp,
            'max_tokens'  => $maxTokens ?? 8000,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $content = trim($response->json()['choices'][0]['message']['content'] ?? '');
        $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
        $content = preg_replace('/\s*```+\s*$/i', '', $content);
        $content = trim($content);

        $decoded = json_decode($content, true);
        if ($decoded !== null) {
            return $decoded;
        }

        $start = strpos($content, '[');
        $end = strrpos($content, ']');
        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($content, $start, $end - $start + 1), true);
            if ($decoded !== null) return $decoded;
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($content, $start, $end - $start + 1), true);
            if ($decoded !== null) return $decoded;
        }

        return null;
    }

    public function listProviders(): array
    {
        $result = [];
        foreach (config('ai.providers', []) as $name => $provider) {
            $models = array_keys($provider['models']);
            $result[$name] = [
                'base_url' => $provider['base_url'],
                'models'   => $models,
            ];
        }

        return $result;
    }

    public function listModels(?string $provider = null): array
    {
        $p = $this->getProvider($provider);

        return array_keys($p['models']);
    }
}
