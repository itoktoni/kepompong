<?php

namespace App\Services\ActivityGenerator;

class MonologGenerator extends GenericGenerator
{
    protected function type(): string { return 'monolog'; }
    protected function label(): string { return 'Monolog'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat naskah monolog untuk anak. Setiap halaman berisi satu bagian monolog: cerita liburan, pidato mini, bercerita dari benda, stand up comedy, review buku.';
    }
}
