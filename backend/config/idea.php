<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Default provider used for all idea types unless overridden.
    | Values fall back to config('services.openai') if not set here.
    |
    */

    'default' => [
        'provider' => env('IDEA_AI_PROVIDER', 'openai'),
        'model'    => env('IDEA_AI_MODEL', env('OPENAI_MODEL', 'MiniMax-M2.7-highspeed')),
        'base_url' => env('IDEA_AI_BASE_URL', env('OPENAI_BASE_URL', 'https://api.openai.com')),
        'api_key'  => env('IDEA_AI_API_KEY', env('OPENAI_API_KEY')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-Type AI Provider Overrides
    |--------------------------------------------------------------------------
    |
    | Override the AI provider/model for specific idea types.
    | Any field left null falls back to the default above.
    |
    | Supported providers: openai (any OpenAI-compatible API)
    |
    */

    'types' => [

        'permainan_edukasi' => [
            'provider' => env('IDEA_EDU_PROVIDER', null),
            'model'    => env('IDEA_EDU_MODEL', null),
            'base_url' => env('IDEA_EDU_BASE_URL', null),
            'api_key'  => env('IDEA_EDU_API_KEY', null),
            'temperature' => 0.8,
            'max_tokens'  => null, // auto-calculated from count
        ],

        'permainan_kerjasama' => [
            'provider' => env('IDEA_TEAM_PROVIDER', null),
            'model'    => env('IDEA_TEAM_MODEL', null),
            'base_url' => env('IDEA_TEAM_BASE_URL', null),
            'api_key'  => env('IDEA_TEAM_API_KEY', null),
            'temperature' => 0.8,
            'max_tokens'  => null,
        ],

        'permainan_aktif' => [
            'provider' => env('IDEA_ACTIVE_PROVIDER', null),
            'model'    => env('IDEA_ACTIVE_MODEL', null),
            'base_url' => env('IDEA_ACTIVE_BASE_URL', null),
            'api_key'  => env('IDEA_ACTIVE_API_KEY', null),
            'temperature' => 0.8,
            'max_tokens'  => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Generation Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'count'      => 8,
        'min_age'    => 3,
        'max_age'    => 8,
        'temperature' => 0.8,
        'max_tokens_multiplier' => 400, // max_tokens = count * multiplier
    ],

];
