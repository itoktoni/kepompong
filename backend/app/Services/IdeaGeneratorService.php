<?php

namespace App\Services;

use App\Contracts\IdeaGeneratorInterface;
use App\Models\Idea;
use App\Services\IdeaGenerator\StorytellingGenerator;
use App\Services\IdeaGenerator\BermainPeranGenerator;
use App\Services\IdeaGenerator\PermainanGenerator;
use App\Services\IdeaGenerator\MonologGenerator;
use App\Services\IdeaGenerator\ProyekKreatifGenerator;
use App\Services\IdeaGenerator\MusikGerakGenerator;
use App\Services\IdeaGenerator\PuzzleGenerator;
use App\Services\IdeaGenerator\MindfulnessGenerator;
use App\Services\IdeaGenerator\OutdoorGenerator;
use App\Services\IdeaGenerator\IlmuPengetahuanGenerator;
use App\Services\IdeaGenerator\TebakTeakanGenerator;
use App\Services\IdeaGenerator\PermainanTanganGenerator;
use App\Services\IdeaGenerator\LatihanOtakGenerator;
use App\Services\IdeaGenerator\KomikGenerator;
use App\Services\IdeaGenerator\WorksheetGenerator;
use App\Services\IdeaGenerator\ColoringGenerator;

class IdeaGeneratorService
{
    private array $generators = [
        'storytelling'     => StorytellingGenerator::class,
        'bermain_peran'    => BermainPeranGenerator::class,
        'permainan'        => PermainanGenerator::class,
        'monolog'          => MonologGenerator::class,
        'proyek_kreatif'   => ProyekKreatifGenerator::class,
        'musik_gerak'      => MusikGerakGenerator::class,
        'puzzle'           => PuzzleGenerator::class,
        'mindfulness'      => MindfulnessGenerator::class,
        'outdoor'          => OutdoorGenerator::class,
        'ilmu_pengetahuan' => IlmuPengetahuanGenerator::class,
        'tebak_teakan'     => TebakTeakanGenerator::class,
        'permainan_tangan' => PermainanTanganGenerator::class,
        'latihan_otak'     => LatihanOtakGenerator::class,
        'komik'            => KomikGenerator::class,
        'worksheet'        => WorksheetGenerator::class,
        'coloring'         => ColoringGenerator::class,
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

    public function generateWithAI(string $type, int $count, array $ages, ?string $agama, array $skills, ?string $theme = null): array
    {
        return $this->getGenerator($type)->generateWithAI($count, $ages, $agama, $skills, $theme);
    }

    public function saveIdeas(array $result, string $type, array $ages, ?string $agama, array $skills, int $count, string $model): int
    {
        $items = $result['items'] ?? [];
        $prompt = $result['prompt'] ?? '';

        $saved = 0;
        foreach ($items as $item) {
            Idea::create([
                'idea_nama'       => $item['name'] ?? '',
                'idea_keterangan' => $item['desc'] ?? '',
                'idea_moral'      => $item['moral'] ?? '',
                'idea_type'       => $type,
                'idea_creator'    => $model,
                'idea_tanggal'    => null,
                'idea_agama'      => $agama ? [$agama] : [],
                'idea_ages'       => $ages,
                'idea_skills'     => $skills,
                'idea_qty'        => $count,
                'idea_prompt'     => $prompt,
            ]);
            $saved++;
        }

        return $saved;
    }
}
