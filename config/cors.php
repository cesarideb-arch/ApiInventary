<?php

return [

    'paths' => ['*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:5173',
        'http://localhost:8000',
        'https://proyectoinventary-production-2098.up.railway.app',
        'https://attractive-balance-production.up.railway.app'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];