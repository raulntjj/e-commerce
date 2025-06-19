<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserControllerTest extends TestCase {
    use DatabaseMigrations;

    /** @test */
    public function it_can_register_a_new_user(): void {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $this->post('/register', $payload);

        $this->assertResponseStatus(201);
        $this->seeInDatabase('users', ['email' => 'john.doe@example.com']);
        $this->seeJson(['name' => 'John Doe']);
    }

    /** @test */
    public function an_authenticated_user_can_get_a_specific_user(): void {
        $auth = $this->authenticate();
        $userToFind = User::factory()->create();

        $this->get('/users/' . $userToFind->uuid, $auth['headers']);

        $this->assertResponseOk();
        $this->seeJson(['uuid' => $userToFind->uuid, 'email' => $userToFind->email]);
    }

    /** @test */
    public function an_authenticated_user_can_get_all_users(): void {
        $auth = $this->authenticate();
        User::factory()->count(5)->create();

        $this->get('/users', $auth['headers']);
        
        $this->assertResponseOk();
        $response = json_decode($this->response->getContent(), true);
        $this->assertCount(6, $response['data']); // 5 criados + 1 autenticado
    }

    /** @test */
    public function an_authenticated_user_can_update_their_own_data(): void {
        $auth = $this->authenticate();
        $user = $auth['user'];
        $payload = ['name' => 'Jane Doe'];

        $this->put('/users/' . $user->uuid, $payload, $auth['headers']);

        $this->assertResponseOk();
        $this->seeInDatabase('users', ['uuid' => $user->uuid, 'name' => 'Jane Doe']);
        $this->seeJson(['name' => 'Jane Doe']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes(): void {
        $this->get('/users');
        $this->assertResponseStatus(401);

        $this->get('/users/some-uuid');
        $this->assertResponseStatus(401);
    }
}