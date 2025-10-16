<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:5173', 
        'http://localhost:8000',
        'https://proyectoinventario.idebmexico.com', // TU FRONTEND
        'https://apiinventario.idebmexico.com',      // TU BACKEND
    ],

    'allowed_origins_patterns' => [
        'http://localhost:\d+',
        'https://.*\.idebmexico\.com', // PATRÓN PARA TODOS TUS SUBDOMINIOS
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];