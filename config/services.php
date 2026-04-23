<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'api_keys' => explode(',', env('API_KEYS', '')),

    'disbursement' => [
        'key' => env('API_KEY_DISBURSEMENT_SYSTEM', ''),
        'url' => env('API_DISBURSEMENT_SYSTEM', ''),
        'enabled' => env('DISBURSEMENT_SYSTEM_ENABLED', false),
    ],

    'portal' => [
        'url' => env('CITIZEN_PORTAL', ''),
        'key' => env('API_KEY_CITIZEN_USERS', ''),
        'fetch' => env('FETCH_FROM_PORTAL', false),
        'mock' => env('PORTAL_MOCK', false),
        'sso' => [
            'secret' => env('SSO_SECRET_KEY', ''),
        ],
        'notification' => [
            'endpoint' => env('NOTIFICATION_ENDPOINT', ''),
            'key' => env('NOTIFICATION_KEY', ''),
        ],
    ],

    'fileserver' => [
        'url' => env('FILESERVER_URL'),
        'api' => env('FILESERVER_API'),
        'enc_key' => env('FILE_ENCRYPTION_KEY'),
    ],
];
