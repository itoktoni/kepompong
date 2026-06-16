<?php

namespace App\Services\Notification;

class NotificationChannelFactory
{
    public static function make(string $channel): ChannelInterface
    {
        return match ($channel) {
            'whatsapp' => new WhatsAppChannel(),
            'telegram' => new TelegramChannel(),
            default => new EmailChannel(),
        };
    }
}
