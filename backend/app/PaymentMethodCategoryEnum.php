<?php

namespace App;

use App\Concerns\EnumTrait;

enum PaymentMethodCategoryEnum: string
{
    use EnumTrait;

    // case BANK_BCA = 'bca';
    // case BANK_BTN = 'btn';
    // case EWALLET_OVO = 'ovo';
    // case EWALLET_DANA = 'dana';
    // case EWALLET_SHOPEEPAY = 'shopeepay';
    // case BANK_MANDIRI = 'mandiri';
    // case BANK_SEABANK = 'seabank';

    case QRIS = 'qris';
    case BANK_BLU = 'blu';
    case EWALLET_GOPAY = 'gopay';

    public function description(): string
    {
        return match ($this) {
            // self::BANK_BCA => 'BCA',
            // self::BANK_BTN => 'BTN',
            // self::EWALLET_OVO => 'OVO',
            // self::EWALLET_DANA => 'DANA',
            // self::EWALLET_SHOPEEPAY => 'ShopeePay',
            // self::BANK_MANDIRI => 'Mandiri',
            // self::BANK_SEABANK => 'SeaBank',

            self::QRIS => 'QRIS',
            self::BANK_BLU => 'blu by BCA Digital',
            self::EWALLET_GOPAY => 'GoPay',
        };
    }

    public function group(): string
    {
        return match ($this) {
            // self::BANK_BCA,
            // self::BANK_BTN,
            // self::EWALLET_SHOPEEPAY,
            // self::EWALLET_OVO,
            // self::EWALLET_DANA,
            // self::BANK_MANDIRI,
            // self::BANK_SEABANK,

            self::QRIS => 'QRIS',
            self::BANK_BLU => 'Bank Transfer',
            self::EWALLET_GOPAY, => 'E-Wallet',
        };
    }
}
