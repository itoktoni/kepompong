<?php

namespace App\Services\ActivityGenerator;

class ProyekKreatifGenerator extends GenericGenerator
{
    protected function type(): string { return 'proyek_kreatif'; }
    protected function label(): string { return 'Proyek Kreatif'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Create art and craft projects for children. Each page contains one project step: leaf collage, origami, finger painting, cardboard robot, paper mosaic, clay crafts.';
    }
}
