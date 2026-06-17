<?php

namespace App\Services;

use App\Models\Activity;
use App\Contracts\ActivityAssetInterface;
use App\Services\ActivityAsset\ComicAsset;
use App\Services\ActivityAsset\ColoringAsset;
use App\Services\ActivityAsset\SingleImageAsset;
use App\Services\ActivityAsset\StoryAsset;
use App\Services\ActivityAsset\WorksheetAsset;
use Illuminate\Http\UploadedFile;

class ActivityAssetService
{
    private array $assets = [
        'storytelling'     => StoryAsset::class,
        'komik'            => ComicAsset::class,
        'coloring'         => ColoringAsset::class,
        'worksheet'        => WorksheetAsset::class,
        'bermain_peran'    => SingleImageAsset::class,
        'permainan'        => SingleImageAsset::class,
        'monolog'          => SingleImageAsset::class,
        'proyek_kreatif'   => SingleImageAsset::class,
        'musik_gerak'      => SingleImageAsset::class,
        'puzzle'           => SingleImageAsset::class,
        'mindfulness'      => SingleImageAsset::class,
        'outdoor'          => SingleImageAsset::class,
        'ilmu_pengetahuan' => SingleImageAsset::class,
        'tebak_teakan'     => SingleImageAsset::class,
        'permainan_tangan' => SingleImageAsset::class,
        'latihan_otak'     => SingleImageAsset::class,
    ];

    public function getAsset(string $type): ActivityAssetInterface
    {
        $class = $this->assets[$type] ?? SingleImageAsset::class;

        return app($class);
    }

    public function processUpload(Activity $activity, UploadedFile $file, ?int $pages = null): array
    {
        $asset = $this->getAsset($activity->type);

        return $asset->process($activity, $file, $pages);
    }
}
