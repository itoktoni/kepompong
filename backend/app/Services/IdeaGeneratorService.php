<?php

namespace App\Services;

use App\Contracts\IdeaGeneratorInterface;
use App\Services\IdeaGenerator\ActiveGameGenerator;
use App\Services\IdeaGenerator\EduGameGenerator;
use App\Services\IdeaGenerator\TeamGameGenerator;

class IdeaGeneratorService
{
    private array $generators = [
        'permainan_edukasi'    => EduGameGenerator::class,
        'permainan_kerjasama'  => TeamGameGenerator::class,
        'permainan_aktif'      => ActiveGameGenerator::class,
    ];

    public function getGenerator(string $type): IdeaGeneratorInterface
    {
        $class = $this->generators[$type] ?? null;

        if (!$class) {
            throw new \InvalidArgumentException("No idea generator for type: {$type}");
        }

        return app($class);
    }

    public function generate(string $type): array
    {
        return $this->getGenerator($type)->generate();
    }

    public function generateWithAI(string $type, int $count, array $ages, ?string $agama, array $skills): array
    {
        return $this->getGenerator($type)->generateWithAI($count, $ages, $agama, $skills);
    }
}
