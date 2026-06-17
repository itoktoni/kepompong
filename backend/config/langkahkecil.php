<?php

return [

    'frontend_url' => env('FRONTEND_URL', env('APP_URL')),

    'trial_days' => env('LANGKAHKECIL_TRIAL_DAYS', 3),

    'payment_unic_digit' => env('LANGKAHKECIL_PAYMENT_UNIC_DIGIT', 2),

    'affiliate' => [
        'register_bonus' => env('AFFILIATE_REGISTER_BONUS', 500),
        'upgrade_commission_rate' => env('AFFILIATE_UPGRADE_COMMISSION_RATE', 15),
        'max_discounts' => env('AFFILIATE_MAX_DISCOUNTS', 3),
        'max_discount_value' => env('AFFILIATE_MAX_discount_value', env('AFFILIATE_UPGRADE_COMMISSION_RATE', 15)),
        'max_discount_nominal' => env('AFFILIATE_MAX_DISCOUNT_NOMINAL', 10000),
    ],

    'cashout' => [
        'minimum' => env('LANGKAHKECIL_CASHOUT_MINIMUM', 50000),
        'admin_rate' => env('LANGKAHKECIL_CASHOUT_ADMIN_RATE', 3),
        'processing_time' => env('LANGKAHKECIL_CASHOUT_PROCESSING_TIME', '1 hari kerja'),
    ],

    'banks' => [
        ['group' => 'Bank', 'items' => [
            ['code' => 'bca', 'name' => 'BCA'],
            ['code' => 'bni', 'name' => 'BNI'],
            ['code' => 'bri', 'name' => 'BRI'],
            ['code' => 'mandiri', 'name' => 'Mandiri'],
            ['code' => 'bsi', 'name' => 'BSI'],
            ['code' => 'cimb', 'name' => 'CIMB Niaga'],
            ['code' => 'danamon', 'name' => 'Danamon'],
            ['code' => 'permata', 'name' => 'Permata'],
            ['code' => 'btn', 'name' => 'BTN'],
            ['code' => 'seabank', 'name' => 'SeaBank'],
            ['code' => 'blu', 'name' => 'blu by BCA Digital'],
        ]],
        ['group' => 'E-Wallet', 'items' => [
            ['code' => 'gopay', 'name' => 'GoPay'],
            ['code' => 'ovo', 'name' => 'OVO'],
            ['code' => 'dana', 'name' => 'DANA'],
            ['code' => 'shopeepay', 'name' => 'ShopeePay'],
            ['code' => 'linkaja', 'name' => 'LinkAja'],
        ]],
    ],

    'verification' => [
        'register_backend' => (bool) env('VERIFICATION_REGISTER_BACKEND', false),
        'register_frontend' => (bool) env('VERIFICATION_REGISTER_FRONTEND', true),
        'gateway' => env('VERIFICATION_GATEWAY', 'whatsapp'),
        'forgot_gateway' => env('FORGOT_PASSWORD_GATEWAY', 'whatsapp'),
        'code_length' => 6,
        'expires_minutes' => 10,
    ],

    'bypass' => [
        'email_verification' => [
            'web' => (bool) env('BYPASS_EMAIL_VERIFICATION_WEB', false),
            'api' => (bool) env('BYPASS_EMAIL_VERIFICATION_API', false),
        ],
    ],

    'whatsapp' => [
        'provider' => env('WHATSAPP_PROVIDER', 'log'),

        'providers' => [
            'fonnte' => [
                'token' => env('FONNTE_TOKEN'),
            ],

            'twilio' => [
                'sid' => env('TWILIO_SID'),
                'token' => env('TWILIO_TOKEN'),
                'from' => env('TWILIO_WHATSAPP_FROM'),
            ],

            'custom' => [
                'url' => env('WHATSAPP_CUSTOM_URL'),
                'token' => env('WHATSAPP_CUSTOM_TOKEN'),
                'method' => env('WHATSAPP_CUSTOM_METHOD', 'POST'),
            ],
        ],
    ],

];
