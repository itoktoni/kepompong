<?php

namespace App\Services;

use App\Contracts\IdeaGeneratorInterface;
use App\Models\Idea;

use App\Services\IdeaGenerator\StorytellingIdea;
use App\Services\IdeaGenerator\BermainPeranIdea;
use App\Services\IdeaGenerator\PermainanIdea;
use App\Services\IdeaGenerator\MonologIdea;
use App\Services\IdeaGenerator\ProyekKreatifIdea;
use App\Services\IdeaGenerator\MusikGerakIdea;
use App\Services\IdeaGenerator\PuzzleIdea;
use App\Services\IdeaGenerator\MindfulnessIdea;
use App\Services\IdeaGenerator\OutdoorIdea;
use App\Services\IdeaGenerator\IlmuPengetahuanIdea;
use App\Services\IdeaGenerator\TebakTeakanIdea;
use App\Services\IdeaGenerator\PermainanTanganIdea;
use App\Services\IdeaGenerator\LatihanOtakIdea;
use App\Services\IdeaGenerator\KomikIdea;
use App\Services\IdeaGenerator\WorksheetIdea;
use App\Services\IdeaGenerator\ColoringIdea;

class IdeaGeneratorService
{
    private array $generators = [
        'storytelling'     => StorytellingIdea::class,
        'bermain_peran'    => BermainPeranIdea::class,
        'permainan'        => PermainanIdea::class,
        'monolog'          => MonologIdea::class,
        'proyek_kreatif'   => ProyekKreatifIdea::class,
        'musik_gerak'      => MusikGerakIdea::class,
        'puzzle'           => PuzzleIdea::class,
        'mindfulness'      => MindfulnessIdea::class,
        'outdoor'          => OutdoorIdea::class,
        'ilmu_pengetahuan' => IlmuPengetahuanIdea::class,
        'tebak_teakan'     => TebakTeakanIdea::class,
        'permainan_tangan' => PermainanTanganIdea::class,
        'latihan_otak'     => LatihanOtakIdea::class,
        'komik'            => KomikIdea::class,
        'worksheet'        => WorksheetIdea::class,
        'coloring'         => ColoringIdea::class,
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
