<?php

namespace App\Services\Notification\WhatsApp;

interface WhatsAppProviderInterface
{
    public function send(string $to, string $message): bool;
}
