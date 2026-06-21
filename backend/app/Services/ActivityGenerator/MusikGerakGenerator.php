<?php

namespace App\Services\ActivityGenerator;

class MusikGerakGenerator extends GenericGenerator
{
    protected function type(): string { return 'musik_gerak'; }
    protected function label(): string { return 'Musik & Gerak'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create songs and movements for children. Each page contains lyrics or movements: dancing, clapping, jumping, walking to rhythm.';
    }
}
