<?php

namespace App\Services\ActivityGenerator;

class TebakTeakanGenerator extends GenericGenerator
{
    protected function type(): string { return 'tebak_teakan'; }
    protected function label(): string { return 'Tebak-tebakan'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat tebak-tebakan dengan clue dan jawaban yang menarik. Setiap halaman berisi satu soal tebak-tebakan.';
    }
}
