<?php

namespace App;

use App\Concerns\EnumTrait;

enum RoleEnum: string
{
    use EnumTrait;

    case USER = 'user';
    case ADMIN = 'admin';
    case DEVELOPER = 'developer';

    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator Utama',
            self::DEVELOPER => 'Developer',
            self::USER => 'Pengguna Biasa',
        };
    }
}
