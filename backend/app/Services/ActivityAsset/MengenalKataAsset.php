<?php

namespace App\Services\ActivityAsset;

use App\Models\Activity;

class MengenalKataAsset extends BaseAsset
{
    public function shouldSplit(): bool
    {
        return true;
    }

    public function getPageCount(Activity $activity): int
    {
        if (!empty($activity->data['slides'])) {
            return count($activity->data['slides']) + 1;
        }

        return 16;
    }
}
