<?php

namespace App\Services\Notification\WhatsApp;

class CustomProvider implements WhatsAppProviderInterface
{
    public function send(string $to, string $message): bool
    {
        $url = config('langkahkecil.whatsapp.providers.custom.url');
        $token = config('langkahkecil.whatsapp.providers.custom.token');
        $method = strtoupper(config('langkahkecil.whatsapp.providers.custom.method', 'POST'));

        if (!$url) {
            \Log::warning('[Custom WhatsApp] URL not configured');
            return false;
        }

        $phone = preg_replace('/[^0-9]/', '', $to);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $headers = ['Content-Type' => 'application/json'];
            if ($token) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }

            $payload = [
                'target' => $phone,
                'to' => $phone,
                'phone' => $phone,
                'message' => $message,
                'text' => $message,
            ];

            $response = \Http::withHeaders($headers)
                ->timeout(30)
                ->$method($url, $payload);

            if ($response->successful()) {
                return true;
            }

            \Log::warning('[Custom WhatsApp] Send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url,
            ]);
            return false;
        } catch (\Throwable $e) {
            \Log::warning('[Custom WhatsApp] Exception: ' . $e->getMessage());
            return false;
        }
    }
}
