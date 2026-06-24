<?php

namespace App\Services\ActivityGenerator;

class MengenalBendaGenerator extends GenericGenerator
{
    protected function type(): string { return 'mengenal_benda'; }
    protected function label(): string { return 'Mengenal Benda'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create content that introduces objects to children. Each page describes one object with its name, shape, color, and function. Use simple and fun language.';
    }
}
