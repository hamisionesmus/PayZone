<?php

namespace App\Repositories;

use App\Models\Payslip;
use App\Config\Database;
use PDO;

class PayslipRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll(int $employeeId = null, int $payrollRunId = null, int $limit = 10, int $offset = 0): array
    {
        $where = [];
        $params = [];

        if ($employeeId) {
            $where[] = "employee_id = ?";
            $params[] = $employeeId;
        }

        if ($payrollRunId) {
            $where[] = "payroll_run_id = ?";
            $params[] = $payrollRunId;
        }

        $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

        $stmt = $this->pdo->prepare("SELECT * FROM payslips $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $params[] = $limit;
        $params[] = $offset;
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        return array_map(fn($row) => new Payslip($row), $data);
    }

    public function findById(int $id): ?Payslip
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payslips WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Payslip($data) : null;
    }

    public function create(array $data): Payslip
    {
        $stmt = $this->pdo->prepare("INSERT INTO payslips (employee_id, payroll_run_id, gross_pay, net_pay, deductions_total, allowances_total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['employee_id'],
            $data['payroll_run_id'],
            $data['gross_pay'],
            $data['net_pay'],
            $data['deductions_total'] ?? 0,
            $data['allowances_total'] ?? 0
        ]);
        $data['id'] = $this->pdo->lastInsertId();
        return new Payslip($data);
    }

    public function update(int $id, array $data): ?Payslip
    {
        $stmt = $this->pdo->prepare("UPDATE payslips SET gross_pay = ?, net_pay = ?, deductions_total = ?, allowances_total = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([
            $data['gross_pay'] ?? null,
            $data['net_pay'] ?? null,
            $data['deductions_total'] ?? null,
            $data['allowances_total'] ?? null,
            $id
        ]);
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM payslips WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteByPayrollRun(int $payrollRunId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM payslips WHERE payroll_run_id = ?");
        return $stmt->execute([$payrollRunId]);
    }

    public function getByPayrollRun(int $payrollRunId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, e.first_name, e.last_name, e.email
            FROM payslips p
            JOIN employees e ON p.employee_id = e.id
            WHERE p.payroll_run_id = ?
            ORDER BY e.last_name, e.first_name
        ");
        $stmt->execute([$payrollRunId]);
        $data = $stmt->fetchAll();
        return array_map(function($row) {
            $payslip = new Payslip($row);
            $payslip->employee_name = $row['first_name'] . ' ' . $row['last_name'];
            $payslip->employee_email = $row['email'];
            return $payslip;
        }, $data);
    }
}