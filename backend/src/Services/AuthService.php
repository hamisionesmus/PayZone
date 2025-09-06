<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Repositories\UserRepository;
use App\Models\User;

class AuthService
{
    private UserRepository $userRepository;
    private string $jwtSecret;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->jwtSecret = getenv('JWT_SECRET') ?: 'defaultsecret';
    }

    public function authenticate(string $username, string $password): ?string
    {
        $user = $this->userRepository->findByUsername($username);
        if (!$user || !password_verify($password, $user->password_hash)) {
            return null;
        }
        return $this->generateToken($user);
    }

    private function generateToken(User $user): string
    {
        $payload = [
            'iss' => 'payroll-system',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
            'role' => $user->role_id
        ];
        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function validateToken(string $token): ?array
    {
        try {
            return (array) JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }
}