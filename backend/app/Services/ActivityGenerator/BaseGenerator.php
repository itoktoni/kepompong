<?php

namespace App\Services\ActivityGenerator;

use App\Contracts\ActivityGeneratorInterface;
use Illuminate\Support\Str;

abstract class BaseGenerator implements ActivityGeneratorInterface
{
    protected function slug(string $title): string
    {
        return Str::slug($title);
    }

    protected function parseAgama(array $input): array
    {
        return !empty($input['agama']) ? [strtolower(trim($input['agama']))] : [];
    }

    protected function parseKeterangan(string $desc): array
    {
        if (empty($desc)) {
            return ['titles' => [], 'latar' => ''];
        }

        $parts = array_map('trim', explode(',', $desc));
        $titles = [];
        $latarParts = [];

        foreach ($parts as $part) {
            if (empty($part)) continue;
            if (stripos($part, 'Latar:') === 0 || stripos($part, 'latar:') === 0) {
                $latarParts[] = trim(preg_replace('/^Latar:\s*/i', '', $part));
            } else {
                $titles[] = $part;
            }
        }

        return [
            'titles' => $titles,
            'latar'  => implode(', ', $latarParts),
        ];
    }

    protected function baseActivityData(string $type, array $result, array $input): array
    {
        $skills = [];
        if (!empty($input['skill'])) {
            $skills = [$input['skill']];
        } elseif (!empty($input['skills'])) {
            $skills = (array) $input['skills'];
        }

        return [
            'type'   => $type,
            'title'  => $result['title'],
            'slug'   => $this->slug($result['title']),
            'desc'   => $result['desc'] ?? '',
            'image'  => 'cover.png',
            'ages'   => $input['ages'] ?? [],
            'status' => 'pending',
            'agama'  => $this->parseAgama($input),
            'skills' => $skills,
        ];
    }
}
