<?php

use App\Http\Responses\ApiResponse;

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    return ApiResponse::success([
        'system' => 'Order Service - eCommerce',
        'description' => 'Serviço de gerenciamento de pedidos para eCommerce',
        'version' => '1.0.0',
        'status' => 'Operacional',
    ], 'Conexão bem-sucedida com o Order Service');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/orders', 'OrderController@getAll');
    $router->get('/orders/{id}', 'OrderController@get');
    $router->post('/orders', 'OrderController@create');
});