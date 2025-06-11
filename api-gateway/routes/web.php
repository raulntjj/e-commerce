<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

$router->get('/', function (): JsonResponse {
    return ApiResponse::success([
        'system' => 'API Gateway - eCommerce',
        'description' => 'Gateway de API para serviços de eCommerce',
        'version' => '1.0.0',
        'status' => 'Operacional',
    ], 'Conexão bem-sucedida com o API Gateway');
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('auth/login', 'AuthController@login');
    $router->post('auth/refresh', 'AuthController@refresh');
    
    $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    $router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router, $methods) {
        foreach ($methods as $method) {
            $router->addRoute($method, '{service}/{path:.*}', ['uses' => 'GatewayController@handle']);
            $router->addRoute($method, '{service}', ['uses' => 'GatewayController@handle']);
        }
    });
});