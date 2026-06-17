<?php

namespace App\Services\ActivityAsset;

use App\Models\Activity;

class ComicAsset extends BaseAsset
{
    public function shouldSplit(): bool
    {
        return true;
    }

    public function getPageCount(Activity $activity): int
    {
        return isset($activity->data['pages']) ? count($activity->data['pages']) + 1 : 16;
    }
}
