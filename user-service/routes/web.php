<?php

use App\Http\Responses\ApiResponse;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return ApiResponse::success([
        'system' => 'User Service - eCommerce',
        'description' => 'Serviço de gerenciamento de usuários para eCommerce',
        'version' => '1.0.0',
        'status' => 'Operacional',
    ], 'Requisição bem-sucedida');
});

$router->post('/auth/login', 'AuthController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/users', 'UserController@getAll');
    $router->get('/users/{id}', 'UserController@get');
    $router->post('/users', 'UserController@create');
    $router->put('/users/{id}', 'UserController@update');
    $router->delete('/users/{id}', 'UserController@delete');
});
