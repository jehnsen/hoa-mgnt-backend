<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Driver
    |--------------------------------------------------------------------------
    | Supported drivers: "log" (development), "semaphore" (Semaphore PH API).
    | Set SMS_DRIVER=semaphore in production .env to enable real SMS delivery.
    */
    'driver'      => env('SMS_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Provider Credentials
    |--------------------------------------------------------------------------
    | Used when driver is "semaphore". Obtain your API key from
    | https://semaphore.co
    */
    'api_key'     => env('SMS_API_KEY', ''),
    'sender_name' => env('SMS_SENDER_NAME', 'HOA'),
    'endpoint'    => env('SMS_ENDPOINT', 'https://api.semaphore.co/api/v4/messages'),
];
