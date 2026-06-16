<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageGeneratorService
{
    public function generate(string $prompt, string $size = '2K', ?string $model = null): ?string
    {
        $baseUrl = rtrim((string) config('services.image.base_url'), '/');
        $apiKey = (string) config('services.image.api_key');
        $model = $model ?: config('services.image.model', 'seedream-4-5-251128');

        try {
            $response = Http::withToken($apiKey)
                ->asJson()
                ->baseUrl($baseUrl)
                ->timeout(180)
                ->retry(2, 2000)
                ->acceptJson()
                ->post('/images/generations', [
                    'model' => $model,
                    'prompt' => $prompt,
                    'size' => $size,
                    'response_format' => 'url',
                    'watermark' => false,
                ]);

            if (!$response->successful()) {
                Log::warning('Image generation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $url = $data['data'][0]['url'] ?? null;

            if (!$url) {
                Log::warning('Image generation no URL', ['data' => $data]);
                return null;
            }

            return $url;
        } catch (\Throwable $e) {
            Log::warning('Image generation exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function download(string $url): ?string
    {
        try {
            $response = Http::timeout(120)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $tmpPath = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tmpPath, $response->body());

            return $tmpPath;
        } catch (\Throwable $e) {
            Log::warning('Image download failed', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
