<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserRepositoryTest extends TestCase {
    use DatabaseMigrations;

    protected UserRepository $userRepository;

    public function setUp(): void {
        parent::setUp();
        $this->userRepository = new UserRepository(new User());
    }

    /** @test */
    public function it_hashes_password_on_user_creation() {
        $password = 'plain-password';
        $user = $this->userRepository->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => $password,
        ]);

        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertNotEquals($password, $user->password);
    }
    
    /** @test */
    public function it_hashes_password_on_user_update() {
        $user = User::factory()->create();
        $newPassword = 'new-plain-password';

        $updatedUser = $this->userRepository->update($user->uuid, ['password' => $newPassword]);
        
        $this->assertTrue(Hash::check($newPassword, $updatedUser->password));
        $this->assertNotEquals($newPassword, $updatedUser->password);
    }
}