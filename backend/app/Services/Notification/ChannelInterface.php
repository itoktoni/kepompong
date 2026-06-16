<?php

namespace App\Services\Notification;

interface ChannelInterface
{
    public function send(string $to, string $message): bool;
}
