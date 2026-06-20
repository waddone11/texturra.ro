<?php

return [

    'name' => env('APP_NAME', 'Laravel'),
    'tel'  => env('APP_TEL', '+491744552303'),
    'shipping_cost' => env('SHIPPING_COST'),
    'free_shipping_min' => env('FREE_SHIPPING_MIN'),

    'store_owner' => [
        'name' => env('COMPANY_NAME', 'TEXTURRA HOME SRL'),
        'legal_address' => env('COMPANY_LEGAL_ADDRESS', 'Municipiul Ploiești, Strada DILIGENȚEI, Nr. 10, Județ Prahova'),
        'fiscal_address' => env('COMPANY_FISCAL_ADDRESS', 'Municipiul Ploiești, Strada DILIGENȚEI, Nr. 10, Județ Prahova'),
        'registration_number' => env('COMPANY_REGISTRATION_NUMBER', 'J29/548/2017'),
        'unique_code' => env('COMPANY_UNIQUE_CODE', '37242038'),
        'caen_code' => env('COMPANY_CAEN_CODE', '4751'),
        'phone' => env('COMPANY_PHONE', '+40748538323'),
        'iban' => env('COMPANY_IBAN', ''),
        'bank' => env('COMPANY_BANK', ''),
        'support' => env('COMPANY_SUPPORT', ''),
        'status' => env('COMPANY_STATUS', 'Comerciant'),
        'registration_date' => env('COMPANY_REGISTRATION_DATE', '2017-03-21'),
        'certificate_series' => env('COMPANY_CERTIFICATE_SERIES', 'B'),
        'certificate_number' => env('COMPANY_CERTIFICATE_NUMBER', '3376727'),
        'certificate_issue_date' => env('COMPANY_CERTIFICATE_ISSUE_DATE', '2017-03-22'),
        'director' => env('COMPANY_DIRECTOR', 'Linca Simionescu'),
    ],

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    'locale' => env('APP_LOCALE', 'ro'),

    'locales' => ['ro', 'en'],

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
