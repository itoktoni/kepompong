<?php

namespace App;

use App\Concerns\EnumTrait;

enum IdeaType: string
{
    use EnumTrait;

    case EDUKASI = 'permainan_edukasi';
    case KERJASAMA = 'permainan_kerjasama';
    case AKTIF = 'permainan_aktif';

    public function description(): string
    {
        return match ($this) {
            self::EDUKASI    => 'Permainan Edukasi',
            self::KERJASAMA  => 'Permainan Kerja Sama',
            self::AKTIF      => 'Permainan Aktif',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::EDUKASI    => '📚',
            self::KERJASAMA  => '🤝',
            self::AKTIF      => '🏃',
        };
    }
}
