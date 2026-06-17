<?php

namespace App\Console\Commands;

use App\Services\AiService;
use Illuminate\Console\Command;

class AiProvider extends Command
{
    protected $signature = 'ai:provider
        {provider? : Show models for this provider}
        {--list : List all providers}';

    protected $description = 'List available AI providers and models';

    public function handle(AiService $ai): int
    {
        $provider = $this->argument('provider');

        if ($provider) {
            return $this->showProvider($ai, $provider);
        }

        return $this->listAll($ai);
    }

    private function listAll(AiService $ai): int
    {
        $providers = $ai->listProviders();
        $default = config('ai.default_provider');

        $this->info("Available AI Providers:");
        $this->newLine();

        foreach ($providers as $name => $info) {
            $isDefault = $name === $default;
            $tag = $isDefault ? ' <comment>(default)</comment>' : '';
            $this->line("  <comment>{$name}</comment>{$tag}");
            $this->line("    URL: {$info['base_url']}");
            $this->line("    Models: " . implode(', ', $info['models']));
            $this->newLine();
        }

        $this->line("Usage:");
        $this->line("  php artisan generate:idea permainan_edukasi --provider=<comment>minimax</comment>");
        $this->line("  php artisan generate:activity storytelling kebersamaan --provider=<comment>deepseek</comment> --model=<comment>deepseek-chat</comment>");
        $this->line("  php artisan generate:image 75 --provider=<comment>groq</comment>");

        return self::SUCCESS;
    }

    private function showProvider(AiService $ai, string $provider): int
    {
        try {
            $info = $ai->getProvider($provider);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Provider: {$provider}");
        $this->line("URL: {$info['base_url']}");
        $this->newLine();

        $defaultModel = $ai->getModel($provider);

        $rows = [];
        foreach ($info['models'] as $model => $cfg) {
            $isDefault = $model === $defaultModel;
            $rows[] = [
                $isDefault ? "{$model} (default)" : $model,
                $cfg['temperature'] ?? '-',
            ];
        }

        $this->table(['Model', 'Temperature'], $rows);

        return self::SUCCESS;
    }
}
