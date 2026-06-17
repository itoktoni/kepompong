<?php

namespace App\Services;

use App\Models\Activity;
use App\Services\ActivityGenerator\ActivityGeneratorInterface;
use App\Services\ActivityGenerator\StoryGenerator;
use App\Services\ActivityGenerator\ComicGenerator;
use App\Services\ActivityGenerator\ColoringGenerator;
use App\Services\ActivityGenerator\WorksheetGenerator;

class ActivityGeneratorService
{
    private array $generators = [
        'storytelling' => StoryGenerator::class,
        'komik'        => ComicGenerator::class,
        'coloring'     => ColoringGenerator::class,
        'worksheet'    => WorksheetGenerator::class,
    ];

    public function getGenerator(string $type): ActivityGeneratorInterface
    {
        $class = $this->generators[$type] ?? null;

        if (!$class) {
            throw new \InvalidArgumentException("No generator for type: {$type}");
        }

        return app($class);
    }

    public function generateContent(string $type, array $input): array
    {
        return $this->getGenerator($type)->generateContent($input);
    }

    public function buildActivityData(string $type, array $result, array $input): array
    {
        return $this->getGenerator($type)->buildActivityData($result, $input);
    }

    public function buildPrompt(string $type, array $result, array $input): string
    {
        return $this->getGenerator($type)->buildPrompt($result, $input);
    }

    public function assetConfig(string $type): array
    {
        return $this->getGenerator($type)->assetConfig();
    }

    public function createActivity(string $type, array $result, array $input): Activity
    {
        $generator = $this->getGenerator($type);

        $data = $generator->buildActivityData($result, $input);
        $data['prompt'] = $generator->buildPrompt($result, $input);

        return Activity::create(array_merge([
            'sort_order' => 0,
            'active'     => true,
            'views'      => 0,
            'created_by' => 1,
            'skills'     => [],
            'notes'      => null,
            'creator'    => null,
        ], $data));
    }
}
