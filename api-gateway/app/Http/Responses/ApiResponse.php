<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ApiResponse {
    public static function success($data = null, string $message = null, int $statusCode = 200): JsonResponse {
        try {
            $responseData = [
                'success' => true,
                'data' => $data,
                'message' => $message ?? 'Operação realizada com sucesso',
                'timestamp' => Carbon::now()->toISOString(),
                'status' => $statusCode
            ];

            return new JsonResponse($responseData, $statusCode);
        } catch (\Exception $e) {
            return self::criticalError();
        }
    }

    public static function error(string $message, int $statusCode = 400, array $errors = null): JsonResponse {
        try {
            $responseData = [
                'success' => false,
                'message' => $message,
                'errors' => $errors,
                'timestamp' => Carbon::now()->toISOString(),
                'status' => $statusCode
            ];

            return new JsonResponse($responseData, $statusCode);
        } catch (\Exception $e) {
            return self::criticalError();
        }
    }

    protected static function criticalError(): JsonResponse {
        return new JsonResponse([
            'success' => false,
            'message' => 'Erro crítico ao processar resposta',
            'status' => 500
        ], 500);
    }
}