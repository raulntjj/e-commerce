<?php

use App\Http\Responses\ApiResponse;

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    return ApiResponse::success([
        'system' => 'Notification Service - eCommerce',
        'version' => '1.0.0',
        'status' => 'Operacional',
    ], 'Conexão bem-sucedida com o Notification Service');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/notifications', 'NotificationController@getByUser');
    $router->patch('/notifications/{id}/read', 'NotificationController@markAsRead');
});