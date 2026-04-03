<?php

return [
    'default_provider' => env('EINVOICE_PROVIDER', 'ey'),
    'providers' => [
        'ey' => [
            'api_url' => env('EINVOICE_EY_API_URL'),
            'api_key' => env('EINVOICE_EY_API_KEY'),
            'username' => env('EINVOICE_EY_USERNAME'),
            'password' => env('EINVOICE_EY_PASSWORD'),
        ],
        'adaequare' => [
            'api_mode' => env('E_INVOICE_API_MODE', 'TEST'),
            'api_url' => env('EINVOICE_ADAEQUARE_API_URL'),
            'client_id' => env('EINVOICE_ADAEQUARE_CLIENT_ID'),
            'client_secret' => env('EINVOICE_ADAEQUARE_CLIENT_SECRET')
        ],
    ],
];