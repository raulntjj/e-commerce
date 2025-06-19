<?php

use App\Http\Responses\ApiResponse;

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return ApiResponse::success([
        'system' => 'User Service - eCommerce',
        'description' => 'Serviço de gerenciamento de usuários para eCommerce',
        'version' => '1.0.0',
        'status' => 'Operacional',
    ], 'Requisição bem-sucedida');
});

$router->post('/auth/login', 'AuthController@login');
$router->post('/register', 'UserController@create');


$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/users', 'UserController@getAll');
    $router->get('/users/{id}', 'UserController@get');
    $router->put('/users/{id}', 'UserController@update');
    $router->delete('/users/{id}', 'UserController@delete');
});