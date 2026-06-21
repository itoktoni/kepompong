<?php

namespace App\Services\ActivityGenerator;

class PermainanTanganGenerator extends GenericGenerator
{
    protected function type(): string { return 'permainan_tangan'; }
    protected function label(): string { return 'Permainan Tangan'; }
    protected function defaultPages(): int { return 6; }
    protected function contentGuide(): string
    {
        return 'Create finger and hand games for children. Each page contains one game: hand clapping, finger puppet, rock paper scissors, mini congklak, rubber jumping.';
    }
}
