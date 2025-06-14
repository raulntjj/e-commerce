<?php

namespace App\Providers;

use Illuminate\Auth\GenericUser;
use Illuminate\Support\ServiceProvider;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthServiceProvider extends ServiceProvider {
    public function boot(): void {
        $this->app['auth']->viaRequest('api', function ($request): GenericUser|null {
            $token = $request->bearerToken();

            if (!$token) {
                return null;
            }

            try {
                $decoded = JWT::decode($token, new Key(config('services.jwt.secret'), 'HS256'));
                
                return new GenericUser(['id' => $decoded->sub, 'name' => $decoded->name]);

            } catch (\Exception $e) {
                return null;
            }
        });
    }
}
