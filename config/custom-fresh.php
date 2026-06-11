<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Always Keep
    |--------------------------------------------------------------------------
    |
    | Tables listed here are always preserved when running "fresh:custom",
    | even when they are not passed to the command. Useful for tables you
    | never want to lose during local development (e.g. "users").
    |
    */

    'always_keep' => [
        'users',
        'personal_access_tokens',
        'password_reset_tokens',
        'barangays',
        'districts',
        'sexes',
        'religions',
        'nationalities',
        'civil_statuses',
        'relationships',
        'educations',
        'assistances',
        'mode_of_assistances',
        'roles',
        'permissions',
        'role_has_permissions',
        'model_has_roles',
        'model_has_permissions',
        'workflow_steps',
        'sessions',
        'handlers',
        'failed_jobs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Patterns
    |--------------------------------------------------------------------------
    |
    | Glob-style patterns (e.g. "oauth_*", "telescope_*") that are expanded
    | against the database tables on every run and merged with the explicit
    | argument. Anything matched here is treated as "preserve".
    |
    */

    'patterns' => [
        // 'oauth_*',
        // 'telescope_*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Confirm In
    |--------------------------------------------------------------------------
    |
    | The list of environments where the command must ask for confirmation
    | before running. Use the "--force" option to bypass the prompt.
    |
    */

    'confirm_in' => [
        'production',
    ],

];
