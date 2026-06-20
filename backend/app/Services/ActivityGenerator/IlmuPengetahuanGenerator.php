<?php

namespace App\Services\ActivityGenerator;

class IlmuPengetahuanGenerator extends GenericGenerator
{
    protected function type(): string { return 'ilmu_pengetahuan'; }
    protected function label(): string { return 'Ilmu Pengetahuan'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat eksperimen sains sederhana untuk anak. Setiap halaman berisi satu langkah eksperimen atau fakta sains.';
    }
}
