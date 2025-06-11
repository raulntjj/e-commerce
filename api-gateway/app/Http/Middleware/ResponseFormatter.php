<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class ResponseFormatter {
    public function handle(Request $request, Closure $next): mixed {
        $response = $next($request);

        if ($response->headers->get('Content-Type') === 'application/json' 
            && isset(json_decode($response->getContent(), true)['success'])) {
            return $response;
        }

        if ($response->isSuccessful()) {
            $content = $response->getOriginalContent();
            
            return ApiResponse::success(
                is_array($content) ? $content : ['result' => $content],
                null,
                $response->getStatusCode()
            );
        }

        return $response;
    }
}