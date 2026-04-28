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
        'enable' => [
            'get' => env('DISBURSEMENT_SYSTEM_ENABLE_GET', false),
            'post' => env('DISBURSEMENT_SYSTEM_ENABLE_POST', false),
        ],
        'endpoint' => env('DISBURSEMENT_SYSTEM_ENDPOINT', ''),
        'key' => env('DISBURSEMENT_SYSTEM_API_KEY', ''),
    ],

    'portal' => [
        'endpoint' => env('PORTAL_ENDPOINT', ''),
        'users' => [
            'enable' => [
                'get' => env('PORTAL_USERS_ENABLE_GET', false),
                'post' => env('PORTAL_USERS_ENABLE_POST', false),
            ],
            'endpoint' => env('PORTAL_USERS_ENDPOINT', ''),
            'key' => env('PORTAL_USERS_API_KEY', ''),
            'sampleUuid' => env('PORTAL_USERS_SAMPLE_UUID', false),
            'mock' => env('PORTAL_MOCK', false),
        ],
        'sso' => [
            'secret' => env('SSO_SECRET_KEY', ''),
        ],
        'notification' => [
            'enable' => [
                'get' => env('PORTAL_NOTIFICATION_ENABLE_GET', false),
                'post' => env('PORTAL_NOTIFICATION_ENABLE_POST', false),
            ],
            'endpoint' => env('PORTAL_NOTIFICATION_ENDPOINT', ''),
            'key' => env('PORTAL_NOTIFICATION_API_KEY', ''),
        ],
    ],

    'fileserver' => [
        'enable' => [
            'get' => env('FILESERVER_ENABLE_GET', false),
            'post' => env('FILESERVER_ENABLE_POST', false),
        ],
        'endpoint' => env('FILESERVER_ENDPOINT', ''),
        'api' => env('FILESERVER_API'),
        'enc_key' => env('FILESERVER_ENCRYPTION_KEY', ''),
    ],

    'sms' => [
        'enable' => [
            'post' => env('SMS_ENABLE_POST', false),
        ],
        'endpoint' => env('SMS_ENDPOINT', ''),
        'key' => env('SMS_API_KEY', ''),
    ],
];
