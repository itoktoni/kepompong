<?php

namespace App\Services\ActivityGenerator;

class TebakTeakanGenerator extends GenericGenerator
{
    protected function type(): string { return 'tebak_teakan'; }
    protected function label(): string { return 'Tebak-tebakan'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create riddles with clues and interesting answers. Each page contains one riddle question.';
    }
}
