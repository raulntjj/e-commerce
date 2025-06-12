<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class AuthController extends Controller {
    public function login(Request $request): JsonResponse {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return ApiResponse::error('Credenciais inválidas', 401);
        }

        $payload = [
            'iss' => 'user-service',
            'sub' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + env('JWT_EXPIRES_IN', 3600),
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        $data = [
            'access_token' => $jwt,
            'token_type' => 'bearer',
            'expires_in' => (int) env('JWT_EXPIRES_IN', 3600),
        ];
        
        return ApiResponse::success($data, 'Login bem-sucedido.');
    }
}