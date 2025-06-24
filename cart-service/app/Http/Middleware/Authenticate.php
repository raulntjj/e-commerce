<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Http\Responses\ApiResponse;

class Authenticate {
    protected $auth;

    public function __construct(Auth $auth) {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $guard = null): mixed {
        if ($this->auth->guard($guard)->guest()) {
            return ApiResponse::error('Unauthorized', 401);
        }

        return $next($request);
    }
}