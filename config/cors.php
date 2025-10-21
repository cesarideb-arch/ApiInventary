<?php
// ARCHIVO: config/cors.php EN TU BACKEND (apiinventario.idebmexico.com)

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://proyectoinventario.idebmexico.com', // ⬅️ FRONTEND que hace peticiones AL BACKEND
    ],

    'allowed_origins_patterns' => [
        'https://.*\.idebmexico\.com',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // ✅ CORRECTO

];