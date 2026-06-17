<?php

namespace App\Services\ActivityAsset;

use App\Models\Activity;

class SingleImageAsset extends BaseAsset
{
    public function shouldSplit(): bool
    {
        return false;
    }

    public function getPageCount(Activity $activity): int
    {
        return 1;
    }
}
