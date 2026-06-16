<?php

namespace App\Events;

use App\Models\Notification as NotificationModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public NotificationModel $notification,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'.$this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.new';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'icon' => $this->notification->icon,
            'iconColor' => $this->notification->icon_color,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            'url' => $this->notification->url,
            'type' => $this->notification->type,
            'read' => false,
            'meta' => $this->notification->meta,
            'time' => 'Baru saja',
            'created_at' => $this->notification->created_at?->toIso8601String(),
        ];
    }
}
