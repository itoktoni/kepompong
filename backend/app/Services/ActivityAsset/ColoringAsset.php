<?php

namespace App\Services\ActivityAsset;

use App\Models\Activity;

class ColoringAsset extends BaseAsset
{
    public function shouldSplit(): bool
    {
        return true;
    }

    public function getPageCount(Activity $activity): int
    {
        return isset($activity->data['items']) ? count($activity->data['items']) + 1 : 12;
    }
}
