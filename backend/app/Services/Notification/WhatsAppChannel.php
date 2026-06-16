<?php

namespace App\Services\Notification;

class WhatsAppChannel implements ChannelInterface
{
    public function send(string $to, string $message): bool
    {
        $token = env('WHATSAPP_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_ID');

        if (!$token || !$phoneId) {
            \Log::info('[WhatsApp] Token/PhoneID not configured. Message would be sent to: ' . $to);
            \Log::info('[WhatsApp] Message: ' . $message);
            return false;
        }

        $phone = preg_replace('/[^0-9]/', '', $to);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $client = new \GuzzleHttp\Client();
            $client->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ],
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::warning('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }
}
