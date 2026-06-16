<?php

namespace App;

use App\Concerns\EnumTrait;

enum PeriodEnum: string
{
    use EnumTrait;

    case D1 = '1d';
    case D3 = '3d';
    case D7 = '7d';
    case D10 = '10d';
    case D14 = '14d';
    case D30 = '30d';
    case M1 = '1m';
    case M3 = '3m';
    case M6 = '6m';
    case Y1 = '1y';

    public function description(): string
    {
        return match ($this) {
            self::D1 => '1 Hari',
            self::D3 => '3 Hari',
            self::D7 => '7 Hari',
            self::D10 => '10 Hari',
            self::D14 => '14 Hari',
            self::D30 => '30 Hari',
            self::M1 => '1 Bulan',
            self::M3 => '3 Bulan',
            self::M6 => '6 Bulan',
            self::Y1 => '1 Tahun',
        };
    }
}
