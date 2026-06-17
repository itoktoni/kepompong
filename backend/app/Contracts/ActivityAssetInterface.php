<?php

namespace App\Contracts;

interface ActivityAssetInterface
{
    public function getFolder(\App\Models\Activity $activity): string;

    public function shouldSplit(): bool;

    public function getPageCount(\App\Models\Activity $activity): int;

    public function process(\App\Models\Activity $activity, \Illuminate\Http\UploadedFile $file, ?int $pages = null): array;
}
