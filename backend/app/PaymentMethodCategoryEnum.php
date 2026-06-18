<?php

namespace App;

use App\Concerns\EnumTrait;

enum PaymentMethodCategoryEnum: string
{
    use EnumTrait;

    case QRIS = 'qris';
    case BANK_BCA = 'bca';
    case BANK_MANDIRI = 'mandiri';
    case BANK_BTN = 'btn';
    case BANK_SEABANK = 'seabank';
    case BANK_BLU = 'blu';
    case EWALLET_GOPAY = 'gopay';
    case EWALLET_OVO = 'ovo';
    case EWALLET_DANA = 'dana';
    case EWALLET_SHOPEEPAY = 'shopeepay';

    public function description(): string
    {
        return match ($this) {
            self::QRIS => 'QRIS',
            self::BANK_BCA => 'BCA',
            self::BANK_MANDIRI => 'Mandiri',
            self::BANK_BTN => 'BTN',
            self::BANK_SEABANK => 'SeaBank',
            self::BANK_BLU => 'blu by BCA Digital',
            self::EWALLET_GOPAY => 'GoPay',
            self::EWALLET_OVO => 'OVO',
            self::EWALLET_DANA => 'DANA',
            self::EWALLET_SHOPEEPAY => 'ShopeePay',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::QRIS => 'QRIS',
            self::BANK_BCA, self::BANK_MANDIRI,
            self::BANK_BTN, self::BANK_SEABANK, self::BANK_BLU => 'Bank Transfer',
            self::EWALLET_GOPAY, self::EWALLET_OVO, self::EWALLET_DANA,
            self::EWALLET_SHOPEEPAY, => 'E-Wallet',
        };
    }
}
