<?php

namespace App\Services\ActivityGenerator;

class MonologGenerator extends GenericGenerator
{
    protected function type(): string { return 'monolog'; }
    protected function label(): string { return 'Monolog'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat naskah monolog dengan karakter dan tema yang relate dengan anak. Setiap halaman berisi satu bagian monolog.';
    }
}
