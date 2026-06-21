<?php

namespace App\Services\ActivityGenerator;

class LatihanOtakGenerator extends GenericGenerator
{
    protected function type(): string { return 'latihan_otak'; }
    protected function label(): string { return 'Latihan Otak'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create brain training exercises for children. Each page contains one brain exercise: counting, remembering, finding differences, sequencing, word chains.';
    }
}
