<?php

return [
    // Production does not automatically enable enforcement.
    'mode' => env('CSP_MODE', 'report-only'),
    'directives' => [
        'default-src' => ["'self'"],
        'script-src' => ["'self'", 'https://cdn.jsdelivr.net', 'https://cdnjs.cloudflare.com', 'https://code.jquery.com', 'https://cdn.datatables.net', 'https://kit.fontawesome.com'],
        'script-src-attr' => ["'none'"],
        // Bootstrap, Metronic, Select2 and charts set inline styles.
        'style-src' => ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net', 'https://cdnjs.cloudflare.com', 'https://cdn.datatables.net', 'https://fonts.googleapis.com', 'https://ka-f.fontawesome.com'],
        'font-src' => ["'self'", 'data:', 'https://cdnjs.cloudflare.com', 'https://fonts.gstatic.com', 'https://ka-f.fontawesome.com'],
        'img-src' => ["'self'", 'data:', 'blob:'],
        'connect-src' => ["'self'", 'https://ka-f.fontawesome.com'],
        'worker-src' => ["'self'", 'blob:'],
        'form-action' => ["'self'"],
        'object-src' => ["'none'"],
        'base-uri' => ["'self'"],
        'frame-ancestors' => ["'none'"],
    ],
];
