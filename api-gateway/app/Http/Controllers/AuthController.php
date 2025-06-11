<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

class AuthController extends Controller {
    public function login(Request $request): JsonResponse {
        try {
            $credentials = $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            // Simulação de autenticação - substitua pela lógica real
            if ($credentials['email'] !== 'user@example.com' || $credentials['password'] !== 'password') {
                return ApiResponse::error('Credenciais inválidas', 401);
            }

            $payload = [
                'iss' => env('APP_URL'),
                'sub' => 1, // ID do usuário
                'name' => 'Usuário Exemplo',
                'email' => 'user@example.com',
                'iat' => time(),
                'exp' => time() + env('JWT_EXPIRES_IN', 3600)
            ];

            $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            return ApiResponse::success([
                'access_token' => $jwt,
                'token_type' => 'bearer',
                'expires_in' => env('JWT_EXPIRES_IN', 3600)
            ], 'Login realizado com sucesso');
            
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Falha no processo de login',
                500,
                ['exception' => $e->getMessage()]
            );
        }
    }
}