<?php declare(strict_types=1);

return [
    'url' => env('YOUSIGN_URL'),
    'api_key' => env('YOUSIGN_API_KEY'),
    'connection_timeout_seconds' => 5,
    'timeout_seconds' => 5,
    'retry_count' => 1,
    'retry_sleep' => 10,

    'custom_experience_id' => env('YOUSIGN_CUSTOM_EXPERIENCE_ID'),
];
