<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserControllerTest extends TestCase {
    use DatabaseMigrations;

    /** @test */
    public function it_can_create_a_user(): void {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $this->post('/users', $payload);

        $this->assertResponseStatus(201);
        $this->seeInDatabase('users', ['email' => 'john.doe@example.com']);
        $this->seeJson(['name' => 'John Doe']);
    }

    /** @test */
    public function it_can_get_a_specific_user(): void {
        $user = User::factory()->create();

        $this->get('/users/' . $user->uuid);

        $this->assertResponseOk();
        $this->seeJson(['uuid' => $user->uuid, 'email' => $user->email]);
    }

    /** @test */
    public function it_can_get_all_users(): void {
        User::factory()->count(5)->create();

        $this->get('/users');
        
        $this->assertResponseOk();
        $this->seeJsonStructure([
            'data' => [
                '*' => ['uuid', 'name', 'email']
            ]
        ]);
    }

    /** @test */
    public function it_can_update_a_user(): void {
        $user = User::factory()->create();
        $payload = ['name' => 'Jane Doe'];

        $this->put('/users/' . $user->uuid, $payload);

        $this->assertResponseOk();
        $this->seeInDatabase('users', ['uuid' => $user->uuid, 'name' => 'Jane Doe']);
        $this->seeJson(['name' => 'Jane Doe']);
    }

    /** @test */
    public function it_can_delete_a_user(): void {
        $user = User::factory()->create();

        $this->delete('/users/' . $user->uuid);

        $this->assertResponseOk();
        $this->seeInDatabase('users', ['uuid' => $user->uuid]); 
        $this->assertNotNull(User::withTrashed()->find($user->uuid)->deleted_at);
    }
}