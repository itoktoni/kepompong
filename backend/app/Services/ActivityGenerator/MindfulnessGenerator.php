<?php

namespace App\Services\ActivityGenerator;

class MindfulnessGenerator extends GenericGenerator
{
    protected function type(): string { return 'mindfulness'; }
    protected function label(): string { return 'Mindfulness'; }
    protected function defaultPages(): int { return 6; }
    protected function contentGuide(): string
    {
        return 'Create mindfulness and reflection exercises for children. Each page contains one exercise: breathing, listening, sensing, gratitude, feelings drawing.';
    }
}
