<?php

namespace App\Services\ActivityGenerator;

class OutdoorGenerator extends GenericGenerator
{
    protected function type(): string { return 'outdoor'; }
    protected function label(): string { return 'Outdoor'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat aktivitas outdoor untuk anak. Setiap halaman berisi satu aktivitas: mengamati, mengumpulkan, menanam, berjalan, berburu harta karun alam.';
    }
}
