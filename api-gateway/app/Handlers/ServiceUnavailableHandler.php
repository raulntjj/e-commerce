<?php

namespace App\Handlers;

use App\Http\Responses\ApiResponse;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;

class ServiceUnavailableHandler {
    public static function handle(RequestException $e): JsonResponse {
        if ($e instanceof ConnectException) {
            return ApiResponse::error(
                'Serviço temporariamente indisponível',
                503
            );
        }

        $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 503;

        return ApiResponse::error(
            'Erro no serviço remoto',
            $statusCode,
            $e->getResponse() ? json_decode($e->getResponse()->getBody(), true) : null
        );
    }
}