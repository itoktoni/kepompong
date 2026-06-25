<?php

return [

    'paths' => ['api/*', 'storage/*', 'broadcasting/*', 'pusher/auth'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', '*'),
        env('WEBSITE_URL', '*'),
        'https://kepompong.itoktoni.com',
        'https://6774-27-124-95-42.ngrok-free.app'
    ],

    'allowed_origins_patterns' => [
        '#^http://localhost(:\d+)?$#', // Matches http://localhost, http://localhost:3000, http://localhost:5173, etc.
        '#^http://127\.0\.0\.1(:\d+)?$#', // Matches http://127.0.0.1 and all its ports
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
