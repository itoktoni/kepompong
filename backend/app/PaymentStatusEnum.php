<?php

namespace App;

use App\Concerns\EnumTrait;

enum PaymentStatusEnum: string
{
    use EnumTrait;

    case PENDING = 'pending';
    case PAID = 'paid';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Lunas',
            self::EXPIRED => 'Kedaluwarsa',
            self::CANCELLED => 'Dibatalkan',
        };
    }
}
