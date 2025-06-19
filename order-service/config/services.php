<?php

return [
    'jwt' => [
        'secret' => env('JWT_SECRET'),
    ],
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'rabbitmq'),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_DEFAULT_USER', 'root'),
        'pass' => env('RABBITMQ_DEFAULT_PASS', 'root'),
    ],
];