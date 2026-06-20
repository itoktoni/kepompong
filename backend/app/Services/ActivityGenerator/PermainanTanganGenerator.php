<?php

namespace App\Services\ActivityGenerator;

class PermainanTanganGenerator extends GenericGenerator
{
    protected function type(): string { return 'permainan_tangan'; }
    protected function label(): string { return 'Permainan Tangan'; }
    protected function defaultPages(): int { return 6; }
    protected function contentGuide(): string
    {
        return 'Buat permainan jari dan tangan untuk anak. Setiap halaman berisi satu gerakan atau permainan tangan.';
    }
}
