<?php

namespace App\Repositories;

use App\Models\Leave;
use App\Config\Database;
use PDO;

class LeaveRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll(int $companyId, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*, e.first_name, e.last_name, e.email as employee_email
            FROM leaves l
            JOIN employees e ON l.employee_id = e.id
            WHERE e.company_id = ?
            ORDER BY l.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$companyId, $limit, $offset]);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new Leave($row), $data);
    }

    public function findById(int $id): ?Leave
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*, e.first_name, e.last_name, e.email as employee_email
            FROM leaves l
            JOIN employees e ON l.employee_id = e.id
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Leave($data) : null;
    }

    public function findByEmployee(int $employeeId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM leaves WHERE employee_id = ? ORDER BY created_at DESC");
        $stmt->execute([$employeeId]);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new Leave($row), $data);
    }

    public function create(array $data): Leave
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO leaves (employee_id, start_date, end_date, type, status, reason)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['employee_id'],
            $data['start_date'],
            $data['end_date'],
            $data['type'],
            $data['status'] ?? 'pending',
            $data['reason'] ?? null
        ]);
        $data['id'] = $this->pdo->lastInsertId();
        return new Leave($data);
    }

    public function update(int $id, array $data): ?Leave
    {
        $stmt = $this->pdo->prepare("
            UPDATE leaves SET
                start_date = ?,
                end_date = ?,
                type = ?,
                status = ?,
                reason = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $data['start_date'],
            $data['end_date'],
            $data['type'],
            $data['status'],
            $data['reason'] ?? null,
            $id
        ]);
        return $this->findById($id);
    }

    public function updateStatus(int $id, string $status): ?Leave
    {
        $stmt = $this->pdo->prepare("UPDATE leaves SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM leaves WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPendingRequests(int $companyId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*, e.first_name, e.last_name, e.email as employee_email
            FROM leaves l
            JOIN employees e ON l.employee_id = e.id
            WHERE e.company_id = ? AND l.status = 'pending'
            ORDER BY l.created_at ASC
        ");
        $stmt->execute([$companyId]);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new Leave($row), $data);
    }

    public function getApprovedLeaves(int $employeeId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM leaves
            WHERE employee_id = ? AND status = 'approved'
            AND ((start_date BETWEEN ? AND ?) OR (end_date BETWEEN ? AND ?))
        ");
        $stmt->execute([$employeeId, $startDate, $endDate, $startDate, $endDate]);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new Leave($row), $data);
    }
}