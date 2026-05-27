<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | allowed_origins is driven by FRONTEND_URL in .env so the same codebase
    | works in local development (http://localhost:3000) and production
    | (https://springdale-hoa.ph) without code changes.
    |
    | For local dev, add to .env:
    |   FRONTEND_URL=http://localhost:3000
    |
    | For production, set the actual Next.js domain:
    |   FRONTEND_URL=https://springdale-hoa.ph
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter([env('FRONTEND_URL')]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
