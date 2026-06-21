<?php

namespace App\Services\ActivityGenerator;

class PermainanGenerator extends GenericGenerator
{
    protected function type(): string { return 'permainan'; }
    protected function label(): string { return 'Permainan'; }
    protected function defaultPages(): int { return 6; }
    protected function contentGuide(): string
    {
        return 'Create fun games for children. Each page contains one game: word guessing, relay, tag, bingo, memory, puzzle.';
    }
}
