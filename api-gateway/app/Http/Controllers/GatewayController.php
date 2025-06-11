<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use App\Services\Router;

class GatewayController extends Controller {
    protected $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function handle(Request $request, $service, $path = ''): mixed {
        if (!config("services.services.{$service}")) {
            return ApiResponse::error(
                'Service not found',
                404,
                null
            );
        }

        return $this->router->route($request, $service, $path ? "/{$path}" : '');
    }
}