<?php

namespace App\Services\ActivityGenerator;

class OutdoorGenerator extends GenericGenerator
{
    protected function type(): string { return 'outdoor'; }
    protected function label(): string { return 'Outdoor'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create outdoor activities for children. Each page contains one activity: observing, collecting, planting, walking, nature treasure hunt.';
    }
}
