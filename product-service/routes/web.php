<?php

use App\Http\Responses\ApiResponse;

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    return ApiResponse::success([
        'system' => 'Product Service - eCommerce',
        'version' => '1.0.0',
        'status' => 'Operacional',
    ], 'Requisição bem-sucedida');
});

// Rotas de produtos protegidas por autenticação
$router->group(['prefix' => 'products', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/', 'ProductController@getAll');
    $router->get('/{id}', 'ProductController@get');
    $router->post('/', 'ProductController@create');
    $router->put('/{id}', 'ProductController@update');
    $router->delete('/{id}', 'ProductController@delete');
});