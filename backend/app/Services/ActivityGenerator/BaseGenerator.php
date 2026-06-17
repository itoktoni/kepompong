<?php

namespace App\Services\ActivityGenerator;

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
- No written text in other panels except cover
- No speech bubbles allowed
- No merged panels, no oversized panels, no rounded corners
- No outer border around canvas
- No objects crossing panel boundaries
- No Page number
- Funny expressions, clear visual storytelling
- Straight vertical and horizontal grid lines only
- Pure white divider lines between panels
- Every scene fully contained inside its own panel
- Reading order left-to-right, top-to-bottom
- Perfect square ratio 1:1 for every panel

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
