<?php

return [

    'frontend_url' => env('FRONTEND_URL', env('APP_URL')),

    'trial_days' => env('LANGKAHKECIL_TRIAL_DAYS', 3),

    'payment_unic_digit' => env('LANGKAHKECIL_PAYMENT_UNIC_DIGIT', 2),

    'default_payment' => env('DEFAULT_PAYMENT_METHOD', 'qris'),

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

    'verification' => [
        'register_backend' => (bool) env('VERIFICATION_REGISTER_BACKEND', false),
        'register_frontend' => (bool) env('VERIFICATION_REGISTER_FRONTEND', true),
        'gateway' => env('VERIFICATION_GATEWAY', 'whatsapp'),
        'forgot_gateway' => env('FORGOT_PASSWORD_GATEWAY', 'whatsapp'),
        'code_length' => 6,
        'expires_minutes' => 10,
        'payment_token' => env('PAYMENT_TOKEN', null)
    ],

    'bypass' => [
        'email_verification' => [
            'web' => (bool) env('BYPASS_EMAIL_VERIFICATION_WEB', false),
            'api' => (bool) env('BYPASS_EMAIL_VERIFICATION_API', false),
        ],
    ],

    'whatsapp' => [
        'provider' => env('WHATSAPP_PROVIDER', 'log'),
        'url' => env('WHATSAPP_URL', 'https://api.fonnte.com/send'),
        'token' => env('WHATSAPP_TOKEN'),
    ],

];
