<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Firebase\JWT\JWT;
use Illuminate\Auth\GenericUser;

abstract class TestCase extends BaseTestCase {
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication() {
        return require __DIR__.'/../bootstrap/app.php';
    }
    
    /**
     * Helper para autenticar um usuário e retornar o token.
     * @param string|null $userId
     * @return array
     */
    protected function authenticate(string $userId = null): array {
        $userId = $userId ?? 'user-uuid-'.uniqid();
        
        // Mock User para o Order Service
        $user = new GenericUser(['id' => $userId, 'name' => 'Test User']);
        $this->actingAs($user);
        
        $secret = config('services.jwt.secret');
        $payload = [
            'iss' => 'user-service-test',
            'sub' => $userId,
            'name' => 'Test User',
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        
        $token = JWT::encode($payload, $secret, 'HS256');

        return [
            'token' => $token,
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'userId' => $userId,
        ];
    }
}