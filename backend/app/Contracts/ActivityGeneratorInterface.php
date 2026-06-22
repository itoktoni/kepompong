<?php

namespace App\Contracts;

interface ActivityGeneratorInterface
{
    public function generateContent(array $input): array;

    public function buildActivityData(array $result, array $input): array;
}
