<?php

namespace App\Services\ActivityGenerator;

class LatihanOtakGenerator extends GenericGenerator
{
    protected function type(): string { return 'latihan_otak'; }
    protected function label(): string { return 'Latihan Otak'; }
    protected function defaultPages(): int { return 8; }
    protected function contentGuide(): string
    {
        return 'Buat latihan otak dan brain training untuk anak. Setiap halaman berisi satu soal latihan otak: menghitung, mengingat, mencari perbedaan, mengurutkan, kata berantai.';
    }
}
