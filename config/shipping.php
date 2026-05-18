<?php

return [
    'default_provider' => env('SHIPPING_PROVIDER', 'delhivery'),

    'providers' => [
        'delhivery' => [
            'environment' => env('DELHIVERY_ENVIRONMENT', 'staging'),
            'base_url' => env('DELHIVERY_BASE_URL', env('DELHIVERY_ENVIRONMENT', 'staging') === 'production'
                ? 'https://track.delhivery.com'
                : 'https://staging-express.delhivery.com'),
            'api_token' => env('DELHIVERY_API_TOKEN'),
            'timeout' => (int) env('DELHIVERY_TIMEOUT', 15),
            'retry_count' => (int) env('DELHIVERY_RETRY_COUNT', 2),
            'retry_sleep_ms' => (int) env('DELHIVERY_RETRY_SLEEP_MS', 250),
            'client_name' => env('DELHIVERY_CLIENT_NAME'),
            'pickup_location_name' => env('DELHIVERY_PICKUP_LOCATION_NAME'),
        ],
    ],
];
