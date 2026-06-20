<?php

namespace App\Services\ActivityGenerator;

class PuzzleGenerator extends GenericGenerator
{
    protected function type(): string { return 'puzzle'; }
    protected function label(): string { return 'Puzzle'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat puzzle dan teka-teki yang melatih logika anak. Setiap halaman berisi satu soal puzzle dengan jawaban.';
    }
}
