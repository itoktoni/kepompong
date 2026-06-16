<?php

namespace App\Services\Notification;

class TelegramChannel implements ChannelInterface
{
    public function send(string $to, string $message): bool
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            \Log::info('[Telegram] Token/ChatID not configured. Message would be sent to: ' . $to);
            \Log::info('[Telegram] Message: ' . $message);
            return false;
        }

        try {
            $client = new \GuzzleHttp\Client();
            $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                ],
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::warning('Telegram send failed: ' . $e->getMessage());
            return false;
        }
    }
}
