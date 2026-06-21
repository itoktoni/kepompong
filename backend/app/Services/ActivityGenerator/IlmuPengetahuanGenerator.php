<?php

namespace App\Services\ActivityGenerator;

class IlmuPengetahuanGenerator extends GenericGenerator
{
    protected function type(): string { return 'ilmu_pengetahuan'; }
    protected function label(): string { return 'Ilmu Pengetahuan'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create simple science experiments and knowledge facts for children. Each page contains one experiment step or science explanation.';
    }
}
