<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Services\ActivityImageService;
use Illuminate\Console\Command;

class GenerateWorksheetImage extends Command
{
    protected $signature = 'generate:worksheet-image
        {id? : Activity ID (omit to process all pending)}
        {--model= : Image model (default: from IMAGE_MODEL env)}
        {--size=2K : Image size (2K, 1024x1024, 512x512)}
        {--pages= : Override page count for splitting (default: from activity data)}
        {--force : Regenerate even if image already exists}';

    protected $description = 'Generate worksheet images using AI (shortcut for generate:image --type=worksheet)';

    public function handle(ActivityImageService $service): int
    {
        return $this->call('generate:image', array_filter([
            'id'      => $this->argument('id'),
            '--type'  => 'worksheet',
            '--model' => $this->option('model'),
            '--size'  => $this->option('size'),
            '--pages' => $this->option('pages'),
            '--force' => $this->option('force'),
        ], fn($v) => $v !== null));
    }
}
