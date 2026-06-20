<?php

namespace App\Services\ActivityGenerator;

class ProyekKreatifGenerator extends GenericGenerator
{
    protected function type(): string { return 'proyek_kreatif'; }
    protected function label(): string { return 'Proyek Kreatif'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat proyek seni dan kerajinan dengan bahan yang mudah didapat. Setiap halaman berisi satu langkah proyek.';
    }
}
