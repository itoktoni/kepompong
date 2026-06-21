<?php

namespace App\Services\ActivityGenerator;

use App\Contracts\ActivityGeneratorInterface;
use Illuminate\Support\Str;

abstract class BaseGenerator implements ActivityGeneratorInterface
{
    protected function gridLabel(int $pages): string
    {
        return match ($pages) {
            4       => '2x2',
            6       => '3x2',
            8       => '4x2',
            9       => '3x3',
            10      => '5x2',
            12      => '4x3',
            16      => '4x4',
            20      => '5x4',
            24      => '6x4',
            default => '4x4',
        };
    }

    protected function commonRules(): string
    {
        return <<<'RULES'
- CRITICAL: Use ONLY simple Indonesian words that children ages 1-10 can understand
- FORBIDDEN words: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable, and ANY other complex/foreign words
- Use simple words like: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik

RULES;
    }

    protected function slug(string $title): string
    {
        return Str::slug($title);
    }

    protected function parseAgama(array $input): array
    {
        return !empty($input['agama']) ? [strtolower(trim($input['agama']))] : [];
    }

    protected function baseActivityData(string $type, array $result, array $input): array
    {
        return [
            'type'   => $type,
            'title'  => $result['title'],
            'slug'   => $this->slug($result['title']),
            'desc'   => $result['desc'] ?? '',
            'image'  => 'cover.png',
            'ages'   => $input['ages'] ?? [],
            'status' => 'pending',
            'agama'  => $this->parseAgama($input),
        ];
    }
}
