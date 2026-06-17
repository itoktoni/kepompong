<?php

namespace App\Services\ActivityGenerator;

interface ActivityGeneratorInterface
{
    public function generateContent(array $input): array;

    public function buildActivityData(array $result, array $input): array;

    public function buildPrompt(array $result, array $input): string;

    /**
     * Asset generation config for this activity type.
     *
     * @return array{
     *   mode: 'grid'|'single',
     *   default_pages: int,
     *   image_size: string,
     *   style: string,
     *   extra_rules: string,
     * }
     */
    public function assetConfig(): array;
}
