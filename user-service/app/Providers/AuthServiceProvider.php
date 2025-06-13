<?php

namespace App\Providers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    public function register(): void {
        //
    }

    public function boot(): void {
        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->bearerToken();
            if (!$token) {
                return null;
            }

            try {
                $decoded = JWT::decode($token, new Key(config('services.jwt.secret'), 'HS256'));
                
                return User::find($decoded->sub);

            } catch (\Exception $e) {
                return null;
            }
        });
    }
}