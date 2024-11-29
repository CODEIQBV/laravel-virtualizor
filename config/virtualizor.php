<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Virtualizor API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for both Admin and Enduser
    | APIs of Virtualizor. You can enable/disable each type and set their
    | respective credentials.
    |
    */

    'admin' => [
        'enabled' => env('VIRTUALIZOR_ADMIN_ENABLED', false),
        'key' => env('VIRTUALIZOR_ADMIN_KEY'),
        'pass' => env('VIRTUALIZOR_ADMIN_PASS'),
        'ip' => env('VIRTUALIZOR_ADMIN_IP'),
        'port' => env('VIRTUALIZOR_ADMIN_PORT', '4085'),
    ],

    'enduser' => [
        'enabled' => env('VIRTUALIZOR_ENDUSER_ENABLED', false),
        'key' => env('VIRTUALIZOR_ENDUSER_KEY'),
        'pass' => env('VIRTUALIZOR_ENDUSER_PASS'),
        'ip' => env('VIRTUALIZOR_ENDUSER_IP'),
        'port' => env('VIRTUALIZOR_ENDUSER_PORT', '4083'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | When debug is enabled, detailed API responses and errors will be logged
    |
    */
    'debug' => env('VIRTUALIZOR_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Default API Type
    |--------------------------------------------------------------------------
    |
    | This option defines which API type should be used by default when no
    | specific type is specified. Options: 'admin', 'enduser'
    |
    */
    'default' => env('VIRTUALIZOR_DEFAULT_API', 'admin'),
];
