<?php

namespace App\Services\IdeaGenerator;

use App\Contracts\IdeaGeneratorInterface;
use App\Services\AiService;
use Illuminate\Support\Facades\Log;

abstract class BaseIdea implements IdeaGeneratorInterface
{
    abstract protected function typeName(): string;

    protected function cleanText(string $text): string
    {
        return preg_replace('/[^\x00-\x7F]/u', '', $text);
    }

    protected function fallback(int $count, ?string $theme = null): array
    {
        $base = $this->generate();

        if ($theme) {
            $base['title'] = "Ide {$this->typeName()} tentang " . ucfirst($theme);
        }

        $base['items'] = array_slice($base['items'], 0, $count);
        foreach ($base['items'] as $index => &$item) {
            $item['num'] = $index + 1;
        }

        $base['source'] = 'fallback';
        return $base;
    }

    protected function getAi(): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.commands.idea') ?: config('ai.default_provider');
        $model = $ai->getModel($provider);

        return [$ai, $provider, $model];
    }

    protected function aiGenerate(string $systemPrompt, string $userPrompt, int $count, ?string $theme = null): array
    {
        $batchSize = 20;

        if ($count <= $batchSize) {
            return $this->aiGenerateSingle($systemPrompt, $userPrompt, $count, $theme);
        }

        return $this->aiGenerateBatched($systemPrompt, $userPrompt, $count, $batchSize, $theme);
    }

    protected function aiGenerateSingle(string $systemPrompt, string $userPrompt, int $count, ?string $theme = null): array
    {
        try {
            [$ai, $provider, $model] = $this->getAi();
            $result = $ai->chat($provider, $model, $systemPrompt, $userPrompt);

            if (!is_array($result)) {
                Log::warning('Idea AI returned null', ['provider' => $provider, 'model' => $model]);
                return $this->fallback($count, $theme);
            }

            return $this->parseResponse($result, $count, $systemPrompt, $userPrompt, $theme);
        } catch (\Throwable $e) {
            Log::warning('Idea AI exception', ['message' => $e->getMessage()]);
            return $this->fallback($count, $theme);
        }
    }

    protected function aiGenerateBatched(string $systemPrompt, string $userPrompt, int $totalCount, int $batchSize, ?string $theme = null): array
    {
        $allItems = [];
        $batches = (int) ceil($totalCount / $batchSize);
        $title = '';
        $fullPrompt = "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userPrompt}";

        for ($batch = 0; $batch < $batches; $batch++) {
            $remaining = $totalCount - count($allItems);
            $currentBatch = min($batchSize, $remaining);
            if ($currentBatch <= 0) break;

            $batchPrompt = $userPrompt . "\n\nGenerate EXACTLY {$currentBatch} UNIQUE items. Each MUST be different.";

            try {
                [$ai, $provider, $model] = $this->getAi();
                $result = $ai->chat($provider, $model, $systemPrompt, $batchPrompt);

                if (!is_array($result)) {
                    Log::warning('Idea AI batch returned null', ['batch' => $batch]);
                    continue;
                }

                $batchResult = $this->parseResponse($result, $currentBatch, $systemPrompt, $batchPrompt, $theme);
                $batchItems = $batchResult['items'] ?? [];

                if (empty($title) && !empty($batchResult['title'])) {
                    $title = $batchResult['title'];
                }

                foreach ($batchItems as $item) {
                    $allItems[] = $item;
                }

                Log::info('Idea AI batch done', ['batch' => $batch + 1, 'of' => $batches, 'got' => count($batchItems)]);
            } catch (\Throwable $e) {
                Log::warning('Idea AI batch exception', ['batch' => $batch, 'msg' => $e->getMessage()]);
            }
        }

        if (empty($allItems)) {
            return $this->fallback($totalCount, $theme);
        }

        foreach ($allItems as $index => &$item) {
            $item['num'] = $index + 1;
        }

        return [
            'title'  => $title,
            'items'  => array_slice($allItems, 0, $totalCount),
            'source' => 'ai',
            'prompt' => $fullPrompt,
        ];
    }

    protected function parseResponse(array $parsed, int $count, string $systemPrompt = '', string $userPrompt = '', ?string $theme = null): array
    {
        $items = array_filter($parsed, fn ($item) =>
            (isset($item['topik']) || isset($item['name'])) &&
            (isset($item['fakta']) || isset($item['desc']))
        );
        $items = array_values($items);

        if (empty($items)) {
            Log::warning('Idea AI no valid items in parsed response');
            return $this->fallback($count, $theme);
        }

        $cleanedItems = [];
        foreach (array_slice($items, 0, $count) as $index => $item) {
            $cleanedItems[] = [
                'num'   => $index + 1,
                'name'  => $this->cleanText($item['topik'] ?? $item['name'] ?? ''),
                'desc'  => $this->cleanText($item['fakta'] ?? $item['desc'] ?? ''),
                'moral' => $this->cleanText($item['moral'] ?? $item['info'] ?? ''),
            ];
        }

        $fullPrompt = "=== SYSTEM ===\n{$systemPrompt}\n\n=== USER ===\n{$userPrompt}";

        return [
            'title'  => $parsed['title'] ?? $cleanedItems[0]['name'] ?? '',
            'items'  => $cleanedItems,
            'source' => 'ai',
            'prompt' => $fullPrompt,
        ];
    }
}
