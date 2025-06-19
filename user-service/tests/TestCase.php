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
     * @param User|null $user
     * @return array
     */
    protected function authenticate(User $user = null): array {
        $user = $user ?? User::factory()->create();

        $secret = config('services.jwt.secret');
        $payload = [
            'iss' => 'user-service-test',
            'sub' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + config('services.jwt.expires_in', 3600),
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return [
            'token' => $token,
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'user' => $user
        ];
    }
}