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

    'cswdo' => [
        'work_hours' => [
            'start' => env('CSWDO_WORK_HOURS_START', 7),
            'end' => env('CSWDO_WORK_HOURS_END', 16),
        ],
        'department_head' => env('CSWDO_DEPT_HEAD', ''),
        'social_welfare_officer' => env('CSWDO_SOCIAL_WELFARE_OFFICER', ''),
        'department_email' => env('CSWDO_DEPARTMENT_EMAIL', ''),
    ],

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
                'get' => env('PORTAL_USERS_GET_ENABLE', env('PORTAL_USERS_ENABLE_GET', false)),
            ],
            'endpoint' => env('PORTAL_USERS_GET_ENDPOINT', env('PORTAL_USERS_ENDPOINT', '')),
            'key' => env('PORTAL_USERS_GET_API_KEY', env('PORTAL_USERS_API_KEY', '')),
            'mock' => env('PORTAL_MOCK', false),
            'seeder' => [
                'preset' => (bool) env('PORTAL_USERS_SEEDER', true),
                'from_portal' => env('PORTAL_USERS_SEED_FROM_PORTAL', false),
            ],
        ],
        'sso' => [
            'secret' => env('PORTAL_USERS_SSO_SECRET_KEY', env('SSO_SECRET_KEY', '')),
        ],
        'notification' => [
            'enable' => [
                'post' => env('PORTAL_NOTIFICATION_POST_ENABLE', env('PORTAL_NOTIFICATION_ENABLE_POST', false)),
            ],
            'endpoint' => env('PORTAL_NOTIFICATION_POST_ENDPOINT', env('PORTAL_NOTIFICATION_ENDPOINT', '')),
            'key' => env('PORTAL_NOTIFICATION_POST_API_KEY', env('PORTAL_NOTIFICATION_API_KEY', '')),
        ],
    ],

    'fileserver' => [
        'mock' => env('FILESERVER_MOCK', false),
        'mock_token' => env('FILESERVER_MOCK_TOKEN', ''),
        'mock_port' => env('FILESERVER_MOCK_PORT', 8099),
        'mock_duplicates' => env('FILESERVER_MOCK_DUPLICATES', 'reject'),
        'mock_max_upload_kb' => env('FILESERVER_MOCK_MAX_UPLOAD_KB', 30720),
        'enable' => [
            'get' => env('FILESERVER_GET_ENABLE', env('FILESERVER_ENABLE_GET', false)),
            'post' => env('FILESERVER_POST_ENABLE', env('FILESERVER_ENABLE_POST', false)),
        ],
        // Complete API URLs and undated system secrets; both methods use the same API contract.
        'get' => [
            'endpoint' => env('FILESERVER_GET_ENDPOINT', env('FILESERVER_ENDPOINT', '')),
            'key' => env('FILESERVER_GET_API_KEY', env('FILESERVER_API_KEY', '')),
        ],
        'post' => [
            'endpoint' => env('FILESERVER_POST_ENDPOINT', env('FILESERVER_ENDPOINT', '')),
            'key' => env('FILESERVER_POST_API_KEY', env('FILESERVER_API_KEY', '')),
        ],
        // Deprecated compatibility keys; the client no longer appends an upload path.
        'endpoint' => env('FILESERVER_ENDPOINT', ''),
        'api' => env('FILESERVER_API'),
        'enc_key' => env('FILESERVER_ENCRYPTION_KEY', ''),
    ],

    'sms' => [
        'enable' => [
            'post' => env('SMS_POST_ENABLE', env('SMS_ENABLE_POST', false)),
        ],
        'endpoint' => env('SMS_POST_ENDPOINT', env('SMS_ENDPOINT', '')),
        'key' => env('SMS_POST_API_KEY', env('SMS_API_KEY', '')),
    ],

    'psa_classification' => [
        'get' => env('PSA_CLASSIFICATION_GET_ENABLE', env('PSA_CLASSIFICATION', false)),
        'endpoint' => env('PSA_CLASSIFICATION_GET_ENDPOINT', env('PSA_CLASSIFICATION_ENDPOINT', '')),
        'version' => env('PSA_CLASSIFICATION_GET_VERSION', env('PSA_CLASSIFICATION_VERSION', '')),
        'api_key' => env('PSA_CLASSIFICATION_GET_API_KEY', env('PSA_CLASSIFICATION_API_KEY', '')),
    ],
];
