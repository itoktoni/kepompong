<?php

namespace App\Contracts;

interface IdeaGeneratorInterface
{
    public function generate(): array;

    public function generateWithAI(int $count, array $ages, ?string $agama, array $skills, ?string $theme = null, int $pages = 9): array;
}
