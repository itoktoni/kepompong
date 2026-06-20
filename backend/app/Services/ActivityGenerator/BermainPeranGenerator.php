<?php

namespace App\Services\ActivityGenerator;

class BermainPeranGenerator extends GenericGenerator
{
    protected function type(): string { return 'bermain_peran'; }
    protected function label(): string { return 'Bermain Peran'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat skenario bermain peran dengan peran dan situasi yang menarik. Setiap halaman berisi satu adegan atau dialog.';
    }
}
