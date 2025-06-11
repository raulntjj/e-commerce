<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler {
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public function render($request, Throwable $exception): JsonResponse {
        // Se estivermos em debug, mostre o erro completo do Lumen
        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

        try {
            if ($exception instanceof ValidationException) {
                return ApiResponse::error(
                    'Dados inválidos',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $exception->errors()
                );
            }

            if ($exception instanceof ModelNotFoundException) {
                return ApiResponse::error(
                    'Recurso não encontrado',
                    Response::HTTP_NOT_FOUND
                );
            }

            if ($exception instanceof NotFoundHttpException) {
                return ApiResponse::error(
                    'Endpoint não encontrado',
                    Response::HTTP_NOT_FOUND
                );
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return ApiResponse::error(
                    'Método não permitido',
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            if ($exception instanceof AuthenticationException) {
                return ApiResponse::error(
                    'Não autenticado',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            if ($exception instanceof HttpException) {
                return ApiResponse::error(
                    $exception->getMessage(),
                    $exception->getStatusCode()
                );
            }

            return ApiResponse::error(
                'Ocorreu um erro inesperado no servidor',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            
        } catch (\Exception $e) {
            // Fallback seguro se ocorrer erro ao tentar formatar a resposta
            return new JsonResponse([
                'success' => false,
                'message' => 'Erro crítico ao processar requisição',
                'status' => 500
            ], 500);
        }
    }
}