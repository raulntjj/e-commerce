<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
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
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public function report(Throwable $exception): void {
        parent::report($exception);
    }

    public function render($request, Throwable $exception): JsonResponse|Response
    {
        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

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
    }
}