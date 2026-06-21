<?php

namespace App\Services\ActivityGenerator;

class MonologGenerator extends GenericGenerator
{
    protected function type(): string { return 'monolog'; }
    protected function label(): string { return 'Monolog'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create monologues for children. Each page contains one monologue section: vacation story, mini speech, talking from an object, stand up comedy, book review.';
    }
}
