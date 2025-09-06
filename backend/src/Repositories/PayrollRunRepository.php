<?php

namespace App\Repositories;

use App\Models\PayrollRun;
use App\Config\Database;
use PDO;

class PayrollRunRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll(int $companyId, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payroll_runs WHERE company_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$companyId, $limit, $offset]);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new PayrollRun($row), $data);
    }

    public function findById(int $id): ?PayrollRun
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payroll_runs WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new PayrollRun($data) : null;
    }

    public function create(array $data): PayrollRun
    {
        $stmt = $this->pdo->prepare("INSERT INTO payroll_runs (company_id, run_date, status, total_amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['company_id'],
            $data['run_date'],
            $data['status'] ?? 'pending',
            $data['total_amount'] ?? 0
        ]);
        $data['id'] = $this->pdo->lastInsertId();
        return new PayrollRun($data);
    }

    public function update(int $id, array $data): ?PayrollRun
    {
        $stmt = $this->pdo->prepare("UPDATE payroll_runs SET run_date = ?, status = ?, total_amount = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([
            $data['run_date'] ?? null,
            $data['status'] ?? null,
            $data['total_amount'] ?? null,
            $id
        ]);
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM payroll_runs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getStats(int $companyId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                COUNT(*) as total_runs,
                SUM(total_amount) as total_amount,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_runs,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_runs
            FROM payroll_runs
            WHERE company_id = ?
        ");
        $stmt->execute([$companyId]);
        return $stmt->fetch();
    }

    public function getEmployeeCount(int $payrollRunId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM payslips WHERE payroll_run_id = ?");
        $stmt->execute([$payrollRunId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}