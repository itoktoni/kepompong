<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Console\Command;

class SendNotification extends Command
{
    protected $signature = 'notify:send
        {--user=* : User ID(s) to send notification to (required, can specify multiple)}
        {--all : Send to all users}
        {--title= : Notification title (required)}
        {--body= : Notification body text}
        {--icon=info : Material icon name}
        {--icon-color=text-primary : Icon color class or hex}
        {--type=info : Notification type (info, success, warning, error, activity, payment)}
        {--url= : Click URL}
        {--no-broadcast : Skip WebSocket broadcast}';

    protected $description = 'Send a notification to one or more users';

    public function handle(): int
    {
        $title = $this->option('title');
        if (empty($title)) {
            $this->error('The --title option is required.');

            return self::FAILURE;
        }

        $userIds = $this->getUserIds();
        if (empty($userIds)) {
            $this->error('No users specified. Use --user=ID or --all.');

            return self::FAILURE;
        }

        $body = $this->option('body');
        $icon = $this->option('icon');
        $iconColor = $this->option('icon-color');
        $type = $this->option('type');
        $url = $this->option('url');
        $broadcast = ! $this->option('no-broadcast');

        $this->info('Sending notification to '.count($userIds).' user(s)...');
        $this->line("Title    : {$title}");
        $this->line('Body     : '.($body ?: '-'));
        $this->line("Icon     : {$icon}");
        $this->line("Type     : {$type}");
        $this->line('URL      : '.($url ?: '-'));
        $this->line('Broadcast: '.($broadcast ? 'Yes' : 'No'));
        $this->newLine();

        $sent = 0;
        $failed = 0;

        foreach ($userIds as $userId) {
            try {
                NotificationController::notify(
                    userId: (int) $userId,
                    title: $title,
                    body: $body,
                    icon: $icon,
                    iconColor: $iconColor,
                    type: $type,
                    url: $url,
                    broadcast: $broadcast,
                );
                $this->line("  [OK] User #{$userId}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("  [FAIL] User #{$userId}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Done! Sent: {$sent}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function getUserIds(): array
    {
        if ($this->option('all')) {
            return User::pluck('id')->toArray();
        }

        $userIds = [];
        foreach ((array) $this->option('user') as $u) {
            foreach (explode(',', $u) as $id) {
                $id = trim($id);
                if (is_numeric($id)) {
                    $userIds[] = (int) $id;
                }
            }
        }

        return array_unique($userIds);
    }
}
