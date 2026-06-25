<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ActivityImageService
{
    public function __construct(
        private ImageGeneratorService $generator,
    ) {}

    public function process(Activity $activity, string $size = '2K', ?string $model = null, ?int $pagesCount = null, bool $force = false): array
    {
        if (empty($activity->prompt)) {
            throw new \InvalidArgumentException('Activity has no prompt.');
        }

        $pagesCount = $pagesCount ?: $this->resolvePagesCount($activity);
        $folder = $this->getFolder($activity);

        if (!$force && Storage::disk('public')->exists($folder)) {
            return ['status' => 'skipped', 'folder' => $folder, 'message' => 'Folder already exists.'];
        }

        $grid = ImageSplitterService::getGrid($pagesCount);
        if (!$grid) {
            throw new \InvalidArgumentException("Unsupported page count: {$pagesCount}.");
        }

        $imageUrl = $this->generator->generate($activity->prompt, $size, $model);
        if (!$imageUrl) {
            throw new \RuntimeException('Image generation failed.');
        }

        $tmpPath = $this->generator->download($imageUrl);
        if (!$tmpPath) {
            throw new \RuntimeException('Image download failed.');
        }

        try {
            $file = new UploadedFile(
                $tmpPath,
                'image.png',
                mime_content_type($tmpPath),
                null,
                true
            );

            $result = ImageSplitterService::split($file, $activity->id, $pagesCount, $folder);

            @unlink($tmpPath);

            return [
                'status' => 'success',
                'folder' => $result['folder'],
                'files'  => $result['files'],
                'grid'   => $result['grid'],
                'pages'  => $pagesCount,
            ];
        } catch (\Throwable $e) {
            @unlink($tmpPath);
            throw $e;
        }
    }

    public function getFolder(Activity $activity): string
    {
        return "images/{$activity->type}/{$activity->slug}";
    }

    public function resolvePagesCount(Activity $activity): int
    {
        if (!empty($activity->data['pages'])) {
            return count($activity->data['pages']) + 1;
        }

        if (!empty($activity->data['slides'])) {
            return count($activity->data['slides']) + 1;
        }

        if (!empty($activity->data['items'])) {
            return count($activity->data['items']) + 1;
        }

        return 16;
    }
}
