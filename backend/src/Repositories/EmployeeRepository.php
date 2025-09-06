<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Config\Database;
use PDO;

class EmployeeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll(int $companyId, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM employees WHERE company_id = ? LIMIT ? OFFSET ?");
        $stmt->execute([$companyId, $limit, $offset]);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new Employee($row), $data);
    }

    public function findById(int $id): ?Employee
    {
        $stmt = $this->pdo->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Employee($data) : null;
    }

    public function create(array $data): Employee
    {
        $stmt = $this->pdo->prepare("INSERT INTO employees (company_id, user_id, first_name, last_name, email, phone, hire_date, salary, position, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['company_id'],
            $data['user_id'] ?? null,
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['hire_date'],
            $data['salary'],
            $data['position'] ?? null,
            $data['department'] ?? null
        ]);
        $data['id'] = $this->pdo->lastInsertId();
        return new Employee($data);
    }

    public function update(int $id, array $data): ?Employee
    {
        $stmt = $this->pdo->prepare("UPDATE employees SET first_name = ?, last_name = ?, email = ?, phone = ?, hire_date = ?, salary = ?, position = ?, department = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['hire_date'],
            $data['salary'],
            $data['position'] ?? null,
            $data['department'] ?? null,
            $id
        ]);
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM employees WHERE id = ?");
        return $stmt->execute([$id]);
    }
}