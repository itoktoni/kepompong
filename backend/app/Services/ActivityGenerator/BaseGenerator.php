<?php

namespace App\Services\ActivityGenerator;

use App\Contracts\ActivityGeneratorInterface;
use App\Models\Pilar;
use App\Models\MasterSkill;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseGenerator implements ActivityGeneratorInterface
{
    public function generateBatchContent(int $count, array $input): array
    {
        $pilars = Pilar::where('pilar_active', true)
            ->orderBy('pilar_sort_order')
            ->get();

        $results = [];
        for ($i = 0; $i < $count; $i++) {
            $variationInput = $input;
            $variationInput['variation'] = $i + 1;

            if ($pilars->isNotEmpty() && empty($variationInput['pilar'])) {
                $randomPilar = $pilars->random();
                $variationInput['pilar'] = $randomPilar->pilar_key;
            }

            try {
                $result = $this->generateContent($variationInput);
                if (!empty($result['title'])) {
                    $results[] = $result;
                }
            } catch (\Throwable $e) {
                Log::error("generateBatchContent item {$i} error: " . $e->getMessage());
            }
        }

        return $results;
    }

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

    protected function cleanTitleForChild(string $raw): string
    {
        $raw = trim($raw);
        if (str_contains($raw, '|')) {
            $parts = array_map('trim', explode('|', $raw));
            return trim($parts[0]);
        }
        return $raw;
    }

    protected function getSkillsContext(): string
    {
        try {
            $skills = MasterSkill::where('skill_active', true)
                ->orderBy('skill_sort_order')
                ->get(['skill_key', 'skill_title', 'skill_emoji', 'skill_desc', 'skill_pilars', 'skill_ages']);

            if ($skills->isEmpty()) return '';

            $lines = [];
            foreach ($skills as $s) {
                $desc = $s->skill_desc ? " — {$s->skill_desc}" : '';
                $lines[] = "{$s->skill_emoji} {$s->skill_title} (key: {$s->skill_key}){$desc}";
            }
            return implode("\n", $lines);
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function resolveSkills(array $result, array $input): array
    {
        if (!empty($input['skill'])) return [$input['skill']];
        if (!empty($input['skills'])) return (array) $input['skills'];
        if (!empty($result['skills'])) return (array) $result['skills'];
        return [];
    }

    protected function resolveAges(array $result, array $input): array
    {
        if (!empty($result['ages'])) {
            $ages = array_map('intval', (array) $result['ages']);
            $ages = array_filter($ages, fn($a) => $a >= 1 && $a <= 10);
            sort($ages);
            if (!empty($ages)) return array_values($ages);
        }
        if (!empty($input['ages'])) return (array) $input['ages'];
        return [3, 4, 5, 6, 7, 8];
    }

    protected function baseActivityData(string $type, array $result, array $input): array
    {
        return [
            'type'   => $type,
            'title'  => $result['title'],
            'slug'   => $this->slug($result['title']),
            'desc'   => $result['desc'] ?? '',
            'image'  => 'cover.png',
            'ages'   => $this->resolveAges($result, $input),
            'status' => 'pending',
            'agama'  => $this->parseAgama($input),
            'skills' => $this->resolveSkills($result, $input),
        ];
    }
}
