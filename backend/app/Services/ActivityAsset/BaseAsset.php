<?php

namespace App\Services\ActivityAsset;

use App\Contracts\ActivityAssetInterface;
use App\Models\Activity;
use App\Services\ImageSplitterService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class BaseAsset implements ActivityAssetInterface
{
    public function getFolder(Activity $activity): string
    {
        return "images/{$activity->type}/{$activity->slug}";
    }

    public function process(Activity $activity, UploadedFile $file, ?int $pages = null): array
    {
        $folder = $this->getFolder($activity);
        $pages = $pages ?: $this->getPageCount($activity);

        if ($this->shouldSplit() && $pages >= 2) {
            return $this->splitAndStore($activity, $file, $pages, $folder);
        }

        return $this->storeSingle($activity, $file, $folder);
    }

    protected function splitAndStore(Activity $activity, UploadedFile $file, int $pages, string $folder): array
    {
        $result = ImageSplitterService::split($file, $activity->id, $pages, $folder);

        $activity->image = 'cover.png';
        $activity->save();

        return [
            'mode'   => 'grid',
            'folder' => $result['folder'],
            'files'  => $result['files'],
            'grid'   => $result['grid'],
        ];
    }

    protected function storeSingle(Activity $activity, UploadedFile $file, string $folder): array
    {
        if (Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->deleteDirectory($folder);
        }

        Storage::disk('public')->makeDirectory($folder);
        $file->store($folder, 'public');

        $activity->image = 'cover.png';
        $activity->save();

        return [
            'mode'   => 'single',
            'folder' => $folder,
            'files'  => ['cover.png'],
        ];
    }
}
