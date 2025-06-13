<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class Authenticate {
    public function handle(Request $request, Closure $next): mixed {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                throw new Exception('Token not provided', 401);
            }

            $decoded = JWT::decode($token, new Key(config('services.jwt.secret'), 'HS256'));
            
            $request->auth = $decoded;
            
            return $next($request);
        } catch (ExpiredException $e) {
            return ApiResponse::error(
                'Token expired',
                401,
                ['exception' => $e->getMessage()]
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Unauthorized',
                401,
                ['exception' => $e->getMessage()]
            );
        }
    }
}