<?php

namespace App\Services\ActivityGenerator;

class PermainanGenerator extends GenericGenerator
{
    protected function type(): string { return 'permainan'; }
    protected function label(): string { return 'Permainan'; }
    protected function defaultPages(): int { return 6; }
    protected function contentGuide(): string
    {
        return 'Buat permainan seru untuk anak. Setiap halaman berisi satu permainan: tebak kata, estafet, kejar-kejaran, bingo, memori, puzzle.';
    }
}
