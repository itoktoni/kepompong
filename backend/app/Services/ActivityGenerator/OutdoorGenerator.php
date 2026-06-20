<?php

namespace App\Services\ActivityGenerator;

class OutdoorGenerator extends GenericGenerator
{
    protected function type(): string { return 'outdoor'; }
    protected function label(): string { return 'Outdoor'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat eksplorasi outdoor dan petualangan alam untuk anak. Setiap halaman berisi satu aktivitas outdoor.';
    }
}
