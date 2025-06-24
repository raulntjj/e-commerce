<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Responses\ApiResponse;

$router->get('/', function () use ($router) {
    return ApiResponse::success([
        'system' => 'Cart Service - eCommerce',
        'version' => '1.0.0',
        'status' => 'Operacional',
    ], 'Requisição bem-sucedida');
});

$router->group(['prefix' => 'cart', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/', 'CartController@get');
    $router->delete('/', 'CartController@clearCart');

    $router->put('/items', 'CartController@upsertItem');
    $router->delete('/items/{productId}', 'CartController@removeItem');
});