<?php

namespace App\Services\ActivityGenerator;

class MusikGerakGenerator extends GenericGenerator
{
    protected function type(): string { return 'musik_gerak'; }
    protected function label(): string { return 'Musik & Gerak'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat lagu dan gerakan untuk anak. Setiap halaman berisi lirik atau gerakan: menari, menepuk, melompat, berjalan mengikuti irama.';
    }
}
