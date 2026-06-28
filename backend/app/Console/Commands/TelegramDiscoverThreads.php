<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramDiscoverThreads extends Command
{
    protected $signature = 'telegram:discover-threads';

    protected $description = 'Discover Telegram group topics (thread IDs) from bot updates';

    public function handle(): int
    {
        $token = config('langkahkecil.telegram.bot_token');

        if (empty($token)) {
            $this->error('TELEGRAM_BOT_TOKEN belum di-set di .env');
            return self::FAILURE;
        }

        $this->line('Mengambil updates dari Telegram...');
        $this->line('Pastikan bot sudah di-add ke group dan Privacy Mode sudah OFF');
        $this->newLine();

        $response = Http::timeout(15)->get("https://api.telegram.org/bot{$token}/getUpdates", [
            'allowed_updates' => json_encode(['message', 'channel_post', 'my_chat_member']),
        ]);

        if (!$response->successful()) {
            $this->error('Gagal menghubungi Telegram API');
            return self::FAILURE;
        }

        $data = $response->json();

        if (empty($data['result'])) {
            $this->warn('Tidak ada updates. Kemungkinan:');
            $this->line('  1. Bot belum di-add ke group');
            $this->line('  2. Privacy Mode masih ON (matikan di @BotFather → Bot Settings → Group Privacy)');
            $this->line('  3. Belum ada pesan yang dikirim setelah bot di-add');
            $this->line('  4. Updates sudah di-consumsi (kirim pesan baru lalu cek lagi)');
            $this->newLine();
            $this->line('Cek manual: https://api.telegram.org/bot' . $token . '/getUpdates');
            return self::FAILURE;
        }

        $groups = [];

        foreach ($data['result'] as $update) {
            $chat = $update['message']['chat'] ?? $update['channel_post']['chat'] ?? null;
            $threadId = $update['message']['message_thread_id'] ?? $update['channel_post']['message_thread_id'] ?? null;
            $text = $update['message']['text'] ?? $update['channel_post']['text'] ?? '';

            if (!$chat) continue;

            $chatId = $chat['id'];
            $chatTitle = $chat['title'] ?? $chat['first_name'] ?? 'unknown';
            $chatType = $chat['type'] ?? 'unknown';

            if (!isset($groups[$chatId])) {
                $groups[$chatId] = [
                    'title' => $chatTitle,
                    'type' => $chatType,
                    'threads' => [],
                ];
            }

            if ($threadId && !isset($groups[$chatId]['threads'][$threadId])) {
                $groups[$chatId]['threads'][$threadId] = [
                    'text' => $text,
                    'date' => date('Y-m-d H:i:s', $update['message']['date'] ?? $update['channel_post']['date'] ?? 0),
                ];
            }
        }

        if (empty($groups)) {
            $this->warn('Tidak ditemukan group.');
            return self::FAILURE;
        }

        foreach ($groups as $chatId => $group) {
            $this->line("━━━ {$group['title']} ━━━");
            $this->line("Chat ID: <comment>{$chatId}</comment>");
            $this->line("Tipe: {$group['type']}");
            $this->newLine();

            if (!empty($group['threads'])) {
                $this->line('Topics/Threads:');
                $rows = [];
                foreach ($group['threads'] as $threadId => $thread) {
                    $rows[] = [$threadId, $thread['text'], $thread['date']];
                }
                $this->table(['Thread ID', 'Pesan Terakhir', 'Tanggal'], $rows);
            } else {
                $this->line('Tidak ditemukan Topics. Group ini mungkin tidak pakai Forum.');
            }

            $this->newLine();
        }

        $this->line('Setelah dapat Thread ID, isi di .env:');
        $this->line('  TELEGRAM_GROUP_ID=<chat_id_group>');
        $this->line('  TELEGRAM_THREAD_INSTAGRAM=<thread_id>');
        $this->line('  TELEGRAM_THREAD_TIKTOK=<thread_id>');
        $this->line('  TELEGRAM_THREAD_TELEGRAM=<thread_id>');

        return self::SUCCESS;
    }
}
