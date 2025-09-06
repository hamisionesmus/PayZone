<?php

namespace App\Repositories;

use App\Models\User;
use App\Config\Database;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public function create(array $data): User
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (company_id, username, email, password_hash, role_id, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['company_id'] ?? null,
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['role_id'],
            $data['is_active'] ?? true
        ]);
        $data['id'] = $this->pdo->lastInsertId();
        return new User($data);
    }

    public function findAll(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new User($row), $data);
    }
}