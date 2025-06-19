<?php

namespace Tests;

use App\Models\User;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Firebase\JWT\JWT;

abstract class TestCase extends BaseTestCase {
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication(): mixed {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Helper para autenticar um usuário e retornar o token.
     * @return array
     */
    protected function authenticate(): array {
        $userPayload = ['id' => 'user-uuid-123', 'name' => 'Test User'];
        $user = new \Illuminate\Auth\GenericUser($userPayload);
        $this->actingAs($user);

        $secret = config('services.jwt.secret');
        $payload = [
            'iss' => 'user-service-test',
            'sub' => $user->id,
            'name' => 'Test User',
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        
        $token = JWT::encode($payload, $secret, 'HS256');

        return [
            'token' => $token,
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ];
    }
}