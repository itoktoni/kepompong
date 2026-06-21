<?php

namespace App\Services\ActivityGenerator;

class ProyekKreatifGenerator extends GenericGenerator
{
    protected function type(): string { return 'proyek_kreatif'; }
    protected function label(): string { return 'Proyek Kreatif'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat proyek seni dan kerajinan untuk anak. Setiap halaman berisi satu langkah proyek: kolase daun, origami, lukisan jari, robot kardus, mozaik kertas, kerajinan tanah liat.';
    }
}
