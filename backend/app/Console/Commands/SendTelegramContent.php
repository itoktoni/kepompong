<?php

namespace App\Console\Commands;

use App\Console\Concerns\UsesAiProvider;
use App\Jobs\SendTelegramContentJob;
use App\Telegram\ContentGenerator;
use App\Telegram\TelegramContentType;
use App\Telegram\TelegramService;
use Illuminate\Console\Command;

class SendTelegramContent extends Command
{
    use UsesAiProvider;

    protected $signature = 'telegram:send-content
        {--type= : Tipe konten (contoh: carousel_instagram, tips_telegram)}
        {--topic= : Topik konten (opsional jika --auto-topics)}
        {--auto-topics : AI generate topik + konten sekaligus dari niche}
        {--count=1 : Jumlah konten (kombinasi dengan --auto-topics)}
        {--message= : Pesan manual (lewati AI)}
        {--image= : Path/URL gambar}
        {--generate-image : Generate gambar via AI}
        {--file= : Baca pesan dari file}
        {--chat-id= : Override chat ID default}
        {--queue : Kirim via queue}
        {--provider= : AI provider}
        {--model= : AI model}';

    protected $description = 'Generate & kirim konten marketing ke Telegram group/channel';

    public function handle(ContentGenerator $generator, TelegramService $telegram): int
    {
        if (!$telegram->isConfigured()) {
            $this->error('Telegram belum dikonfigurasi. Set TELEGRAM_BOT_TOKEN dan TELEGRAM_CHAT_ID di .env');
            return self::FAILURE;
        }

        $niche = config('langkahkecil.telegram.niche');
        $this->line("Niche: <comment>{$niche}</comment>");
        $this->newLine();

        $message = $this->option('message');
        $file = $this->option('file');
        $typeName = $this->option('type');

        if ($file) {
            if (!file_exists($file)) {
                $this->error("File tidak ditemukan: {$file}");
                return self::FAILURE;
            }
            $message = file_get_contents($file);
        }

        $type = null;
        if ($typeName) {
            $type = TelegramContentType::tryFrom($typeName);
            if (!$type) {
                $this->error("Tipe tidak dikenal: {$typeName}");
                $this->line('Tipe yang tersedia:');
                foreach (TelegramContentType::cases() as $case) {
                    $this->line("  <comment>{$case->value}</comment> — {$case->emoji()} {$case->description()}");
                }
                return self::FAILURE;
            }
        }

        if (empty($message) && !$this->option('auto-topics') && empty($this->option('topic'))) {
            $this->error('Butuh salah satu: --topic, --auto-topics, atau --message');
            $this->line('');
            $this->line('Contoh penggunaan:');
            $this->line('  <comment>php artisan telegram:send-content --type=tips_telegram --topic="aktivitas outdoor"</comment>');
            $this->line('  <comment>php artisan telegram:send-content --type=reels_instagram --auto-topics --count=2</comment>');
            $this->line('  <comment>php artisan telegram:send-content --message="Halo Ayah/Bunda!"</comment>');
            $this->line('');
            $this->line('Tipe yang tersedia:');
            foreach (TelegramContentType::cases() as $case) {
                $this->line("  <comment>{$case->value}</comment> — {$case->emoji()} {$case->description()}");
            }
            return self::FAILURE;
        }

        if (!$type) {
            $type = TelegramContentType::TIPS_TELEGRAM;
            $this->line("Tipe tidak ditentukan, default ke: <comment>{$type->value}</comment>");
        }

        $chatId = $this->option('chat-id') ?: $type->chatId();
        $threadId = $type->threadId();
        $image = $this->option('image');
        $count = max(1, (int) $this->option('count'));

        $threadInfo = $threadId ? " | Thread: {$threadId}" : '';
        $this->line("Platform: <comment>{$type->platform()}</comment> | Group: <comment>{$chatId}</comment>{$threadInfo}");

        if ($this->option('queue')) {
            SendTelegramContentJob::dispatch(
                topic: $this->option('topic'),
                type: $typeName,
                message: $message,
                image: $image,
                chatId: $chatId,
                autoTopics: $this->option('auto-topics'),
                count: $count,
                generateImage: $this->option('generate-image'),
                provider: $this->option('provider'),
                model: $this->option('model'),
            );
            $this->info('Konten dikirim ke antrian.');
            return self::SUCCESS;
        }

        [$ai, $provider, $model] = $this->resolveAi();

        try {
            if ($this->option('auto-topics')) {
                return $this->sendBatch($generator, $telegram, $type, $count, $provider, $model, $chatId, $image, $threadId);
            }

            $topic = $this->option('topic');

            if ($topic) {
                $this->line("Generating {$type->emoji()} {$type->description()} tentang: {$topic}");
                $this->line("Provider: {$provider} | Model: {$model}");
                $this->newLine();
                $message = $generator->generateAndFormat($type, $topic, $provider, $model);

                $this->newLine();
                $this->line('── Preview ──');
                $this->line($message);
                $this->newLine();
            }

            if ($this->option('generate-image') && $topic) {
                $this->line('Generating gambar...');
                $generatedImage = $generator->generateImage($topic);
                if ($generatedImage) {
                    $image = $generatedImage;
                    $this->line('Gambar berhasil di-generate.');
                } else {
                    $this->warn('Generate gambar gagal, kirim text saja.');
                }
            }

            $this->line('Mengirim ke Telegram...');
            $result = $telegram->sendContent($message, $image, $chatId, [], $threadId);
            $messageId = $result['result']['message_id'] ?? 'unknown';
            $this->info("Terkirim! Message ID: {$messageId}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function sendBatch(
        ContentGenerator $generator,
        TelegramService $telegram,
        TelegramContentType $type,
        int $count,
        ?string $provider,
        ?string $model,
        ?string $chatId,
        ?string $image,
        ?int $threadId,
    ): int {
        $this->line("Generating {$count} konten + topik sekaligus (1x AI call)...");
        $this->line("Provider: {$provider} | Model: {$model}");
        $this->newLine();

        $items = $generator->generateBatch($type, $count, $provider, $model);
        $formatted = $generator->formatBatch($type, $items);

        if (empty($formatted)) {
            $this->error('Gagal generate konten.');
            return self::FAILURE;
        }

        $this->table(['#', 'Topik', 'Pilar', 'Alasan'], array_map(fn($f, $i) => [
            (string) ($i + 1),
            (string) ($f['topik'] ?? ''),
            (string) ($f['pilar'] ?? ''),
            (string) ($f['alasan'] ?? ''),
        ], $formatted, array_keys($formatted)));

        $this->newLine();

        $sent = 0;
        $failed = 0;

        foreach ($formatted as $i => $item) {
            $topic = $item['topik'] ?? '';
            $message = $item['message'] ?? '';
            $num = $i + 1;

            if (empty($message)) {
                $this->warn("[{$num}/{$count}] Konten kosong untuk: {$topic}, skip.");
                $failed++;
                continue;
            }

            $this->line("[{$num}/{$count}] Mengirim: {$topic}");

            try {
                $sendImage = $image;

                if ($this->option('generate-image') && $topic) {
                    $generatedImage = $generator->generateImage($topic);
                    if ($generatedImage) $sendImage = $generatedImage;
                }

                $result = $telegram->sendContent($message, $sendImage, $chatId, [], $threadId);
                $messageId = $result['result']['message_id'] ?? 'unknown';
                $this->info("  Terkirim! Message ID: {$messageId}");
                $sent++;

                if ($i < count($formatted) - 1) {
                    sleep(2);
                }
            } catch (\Throwable $e) {
                $this->error("  Gagal: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Selesai! Terkirim: {$sent}, Gagal: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
