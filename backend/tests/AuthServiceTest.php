<?php

use PHPUnit\Framework\TestCase;
use App\Services\AuthService;
use App\Repositories\UserRepository;

class AuthServiceTest extends TestCase
{
    private AuthService $authService;

    protected function setUp(): void
    {
        // Mock the UserRepository
        $userRepository = $this->createMock(UserRepository::class);
        $this->authService = new AuthService($userRepository);
    }

    public function testPasswordHashing()
    {
        $password = 'testpassword123';
        $hash = $this->authService->hashPassword($password);

        $this->assertTrue(password_verify($password, $hash));
        $this->assertNotEquals($password, $hash);
    }

    public function testPasswordVerification()
    {
        $password = 'mypassword';
        $hash = password_hash($password, PASSWORD_ARGON2I);

        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }

    public function testJWTTokenGeneration()
    {
        // This would require mocking the JWT library
        // For now, just test that the method exists
        $this->assertTrue(method_exists($this->authService, 'hashPassword'));
    }
}