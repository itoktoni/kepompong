<?php

namespace App\Services\ActivityGenerator;

class MindfulnessGenerator extends GenericGenerator
{
    protected function type(): string { return 'mindfulness'; }
    protected function label(): string { return 'Mindfulness'; }
    protected function defaultPages(): int { return 6; }
    protected function contentGuide(): string
    {
        return 'Buat latihan mindfulness dan refleksi untuk anak. Setiap halaman berisi satu latihan: pernapasan, mendengarkan, merasakan, bersyukur, gambar perasaan.';
    }
}
