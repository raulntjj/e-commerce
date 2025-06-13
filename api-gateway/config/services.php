<?php

return [
    'services' => [
        'user-service' => [
            'base_uri' => env('USER_SERVICE_URL', 'http://user-service'),
            'timeout' => env('USER_SERVICE_TIMEOUT', 2),
        ],
        'order-service' => [
            'base_uri' => env('ORDER_SERVICE_URL', 'http://order-service'),
            'timeout' => env('ORDER_SERVICE_TIMEOUT', 2),
        ],
        'product-service' => [
            'base_uri' => env('PRODUCT_SERVICE_URL', 'http://product-service'),
            'timeout' => env('PRODUCT_SERVICE_TIMEOUT', 2),
        ],
        'cart-service' => [
            'base_uri' => env('CART_SERVICE_URL', 'http://cart-service:3000'),
            'timeout' => env('CART_SERVICE_TIMEOUT', 2),
        ],
        'payment-service' => [
          'base_uri' => env('PRODUCT_SERVICE_URL', 'http://product-service:3000'),
          'timeout' => env('PRODUCT_SERVICE_TIMEOUT', 2),
        ],
    ],
    'circuit_break' => [
        'failure_threshold' => 3,
        'success_threshold' => 2,
        'timeout' => 60,
    ],
];