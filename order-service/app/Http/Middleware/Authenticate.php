<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Authenticate {
    public function handle(Request $request, Closure $next): mixed {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                throw new Exception('Token não fornecido.', 401);
            }

            $decoded = JWT::decode($token, new Key(config('services.jwt.secret'), 'HS256'));
            $request->auth = $decoded;
            
            return $next($request);
        } catch (ExpiredException $e) {
            return ApiResponse::error('Token expirado', 401);
        } catch (Exception $e) {
            return ApiResponse::error('Não autorizado', 401, ['exception' => $e->getMessage()]);
        }
    }
}