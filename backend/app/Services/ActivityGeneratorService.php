<?php

namespace App\Services;

use App\Models\Activity;
use App\Contracts\ActivityGeneratorInterface;
use App\Services\ActivityGenerator\ComicGenerator;
use App\Services\ActivityGenerator\ColoringGenerator;
use App\Services\ActivityGenerator\WorksheetGenerator;
use App\Services\ActivityGenerator\BermainPeranGenerator;
use App\Services\ActivityGenerator\PermainanGenerator;
use App\Services\ActivityGenerator\MonologGenerator;
use App\Services\ActivityGenerator\ProyekKreatifGenerator;
use App\Services\ActivityGenerator\MusikGerakGenerator;
use App\Services\ActivityGenerator\PuzzleGenerator;
use App\Services\ActivityGenerator\MindfulnessGenerator;
use App\Services\ActivityGenerator\OutdoorGenerator;
use App\Services\ActivityGenerator\IlmuPengetahuanGenerator;
use App\Services\ActivityGenerator\PermainanTanganGenerator;
use App\Services\ActivityGenerator\LatihanOtakGenerator;
use App\Services\ActivityGenerator\StoryTellingGenerator;
use App\Services\ActivityGenerator\MengenalKataGenerator;
use App\Services\ActivityGenerator\TebakTebakanGenerator;

class ActivityGeneratorService
{
    private array $generators = [
        'storytelling'     => StoryTellingGenerator::class,
        'bermain_peran'    => BermainPeranGenerator::class,
        'permainan'        => PermainanGenerator::class,
        'monolog'          => MonologGenerator::class,
        'proyek_kreatif'   => ProyekKreatifGenerator::class,
        'musik_gerak'      => MusikGerakGenerator::class,
        'puzzle'           => PuzzleGenerator::class,
        'mindfulness'      => MindfulnessGenerator::class,
        'outdoor'          => OutdoorGenerator::class,
        'ilmu_pengetahuan' => IlmuPengetahuanGenerator::class,
        'tebak_tebakan'    => TebakTebakanGenerator::class,
        'permainan_tangan' => PermainanTanganGenerator::class,
        'latihan_otak'     => LatihanOtakGenerator::class,
        'komik'            => ComicGenerator::class,
        'worksheet'        => WorksheetGenerator::class,
        'coloring'         => ColoringGenerator::class,
        'mengenal_kata'   => MengenalKataGenerator::class,
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

    public function createActivity(string $type, array $result, array $input): Activity
    {
        $generator = $this->getGenerator($type);

        $data = $generator->buildActivityData($result, $input);

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
