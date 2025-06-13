<?php

namespace App\Providers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    public function boot(): void {
        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->bearerToken();
            if (!$token) {
                return null;
            }

            try {
                $decoded = JWT::decode($token, new Key(config('services.jwt.secret'), 'HS256'));

                return (object) ['uuid' => $decoded->sub];

            } catch (\Exception $e) {
                return null;
            }
        });
    }
}