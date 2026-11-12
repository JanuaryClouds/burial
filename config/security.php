<?php

return [
    'login' => [
        'max_failures' => 5,
        'window_seconds' => 60,
        'cooldown_seconds' => 60,
    ],
    'images' => [
        'max_width' => 8000,
        'max_height' => 8000,
        'max_pixels' => 25000000,
    ],
    'generation_per_minute' => 10,
    'downloads_per_minute' => 120,
];
