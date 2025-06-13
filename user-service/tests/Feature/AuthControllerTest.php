<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthControllerTest extends TestCase {
    use DatabaseMigrations;

    /** @test */
    public function it_should_authenticate_a_user_and_return_a_jwt_token(): void {

        $password = 'secret123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);


        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertResponseOk();
        
        $this->seeJsonStructure([
            'success',
            'data' => [
                'access_token',
                'token_type',
                'expires_in'
            ],
            'message',
            'timestamp',
            'status'
        ]);
    }

    /** @test */
    public function it_should_return_error_for_invalid_credentials(): void {
        $this->post('/auth/login', [
            'email' => 'invalid@email.com',
            'password' => 'invalidpassword',
        ]);

        $this->assertResponseStatus(401);
        $this->seeJson([
            'success' => false,
            'message' => 'Credenciais inválidas'
        ]);
    }
}