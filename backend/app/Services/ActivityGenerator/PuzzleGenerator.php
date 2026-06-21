<?php

namespace App\Services\ActivityGenerator;

class PuzzleGenerator extends GenericGenerator
{
    protected function type(): string { return 'puzzle'; }
    protected function label(): string { return 'Puzzle'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create puzzles and brain teasers for children. Each page contains one puzzle: matching, sequencing, finding pairs, completing patterns, maze.';
    }
}
