<?php

return [
    'jwt' => [
        'secret' => env('JWT_SECRET'),
        'expires_in' => env('JWT_EXPIRES_IN', 3600),
    ],
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'rabbitmq'),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_DEFAULT_USER', 'root'),
        'pass' => env('RABBITMQ_DEFAULT_PASS', 'root'),
    ],
];