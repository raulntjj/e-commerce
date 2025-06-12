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
    $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    foreach ($methods as $method) {
        $router->addRoute($method, '{service}[/{path:.*}]', 'GatewayController@handle');
    }
});