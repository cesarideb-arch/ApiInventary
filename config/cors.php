<?php
// ARCHIVO: config/cors.php EN TU BACKEND (apiinventario.idebmexico.com)

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://proyectoinventario.idebmexico.com', // PERMITE TU FRONTEND
    ],

    'allowed_origins_patterns' => [
        'https://.*\.idebmexico\.com', // PATRÓN PARA FUTUROS SUBDOMINIOS
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];