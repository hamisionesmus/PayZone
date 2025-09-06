<?php

namespace App\Services;

use App\Config\Database;
use PDO;

class ReportsService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Generate payroll summary report
     */
    public function getPayrollReport(int $companyId, string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? date('Y-m-01'); // First day of current month
        $endDate = $endDate ?? date('Y-m-t'); // Last day of current month

        $stmt = $this->pdo->prepare("
            SELECT
                pr.id,
                pr.run_date,
                pr.status,
                pr.total_amount,
                COUNT(p.id) as employee_count,
                AVG(p.gross_pay) as avg_gross_pay,
                AVG(p.net_pay) as avg_net_pay,
                SUM(p.deductions_total) as total_deductions,
                SUM(p.allowances_total) as total_allowances
            FROM payroll_runs pr
            LEFT JOIN payslips p ON pr.id = p.payroll_run_id
            WHERE pr.company_id = ? AND pr.run_date BETWEEN ? AND ?
            GROUP BY pr.id
            ORDER BY pr.run_date DESC
        ");

        $stmt->execute([$companyId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }

    /**
     * Generate employee salary report
     */
    public function getEmployeeSalaryReport(int $companyId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                e.id,
                e.first_name,
                e.last_name,
                e.email,
                e.salary,
                e.position,
                e.department,
                e.hire_date,
                COALESCE(AVG(p.net_pay), 0) as avg_monthly_pay,
                COUNT(p.id) as payslips_count
            FROM employees e
            LEFT JOIN payslips p ON e.id = p.employee_id
            WHERE e.company_id = ?
            GROUP BY e.id
            ORDER BY e.salary DESC
        ");

        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate leave summary report
     */
    public function getLeaveReport(int $companyId, string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? date('Y-01-01'); // Start of year
        $endDate = $endDate ?? date('Y-12-31'); // End of year

        $stmt = $this->pdo->prepare("
            SELECT
                e.first_name,
                e.last_name,
                e.email,
                e.position,
                e.department,
                COUNT(l.id) as total_leaves,
                SUM(CASE WHEN l.status = 'approved' THEN 1 ELSE 0 END) as approved_leaves,
                SUM(CASE WHEN l.status = 'pending' THEN 1 ELSE 0 END) as pending_leaves,
                SUM(CASE WHEN l.status = 'rejected' THEN 1 ELSE 0 END) as rejected_leaves,
                SUM(CASE WHEN l.status = 'approved' THEN DATEDIFF(l.end_date, l.start_date) + 1 ELSE 0 END) as total_leave_days
            FROM employees e
            LEFT JOIN leaves l ON e.id = l.employee_id AND l.start_date BETWEEN ? AND ?
            WHERE e.company_id = ?
            GROUP BY e.id
            ORDER BY total_leave_days DESC
        ");

        $stmt->execute([$startDate, $endDate, $companyId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate compliance report
     */
    public function getComplianceReport(int $companyId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                'Total Employees' as metric,
                COUNT(*) as value
            FROM employees
            WHERE company_id = ?

            UNION ALL

            SELECT
                'Active Users' as metric,
                COUNT(*) as value
            FROM users
            WHERE company_id = ? AND is_active = 1

            UNION ALL

            SELECT
                'Payroll Runs This Month' as metric,
                COUNT(*) as value
            FROM payroll_runs
            WHERE company_id = ? AND MONTH(run_date) = MONTH(CURRENT_DATE()) AND YEAR(run_date) = YEAR(CURRENT_DATE())

            UNION ALL

            SELECT
                'Pending Leave Requests' as metric,
                COUNT(*) as value
            FROM leaves l
            JOIN employees e ON l.employee_id = e.id
            WHERE e.company_id = ? AND l.status = 'pending'

            UNION ALL

            SELECT
                'Audit Logs This Month' as metric,
                COUNT(*) as value
            FROM audit_logs
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ");

        $stmt->execute([$companyId, $companyId, $companyId, $companyId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate department-wise salary report
     */
    public function getDepartmentSalaryReport(int $companyId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                COALESCE(e.department, 'Unassigned') as department,
                COUNT(e.id) as employee_count,
                AVG(e.salary) as avg_salary,
                MIN(e.salary) as min_salary,
                MAX(e.salary) as max_salary,
                SUM(e.salary) as total_salary
            FROM employees e
            WHERE e.company_id = ?
            GROUP BY e.department
            ORDER BY total_salary DESC
        ");

        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate monthly payroll trend
     */
    public function getPayrollTrend(int $companyId, int $months = 12): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                DATE_FORMAT(pr.run_date, '%Y-%m') as month,
                COUNT(DISTINCT pr.id) as payroll_runs,
                SUM(pr.total_amount) as total_payroll,
                AVG(pr.total_amount) as avg_payroll_per_run,
                COUNT(p.id) as total_payslips
            FROM payroll_runs pr
            LEFT JOIN payslips p ON pr.id = p.payroll_run_id
            WHERE pr.company_id = ?
                AND pr.run_date >= DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(pr.run_date, '%Y-%m')
            ORDER BY month DESC
        ");

        $stmt->execute([$companyId, $months]);
        return $stmt->fetchAll();
    }

    /**
     * Generate employee attendance/leave summary
     */
    public function getEmployeeAttendanceReport(int $companyId, string $year = null): array
    {
        $year = $year ?? date('Y');

        $stmt = $this->pdo->prepare("
            SELECT
                e.id,
                e.first_name,
                e.last_name,
                e.position,
                e.department,
                COUNT(CASE WHEN l.status = 'approved' AND YEAR(l.start_date) = ? THEN 1 END) as approved_leaves,
                SUM(CASE WHEN l.status = 'approved' AND YEAR(l.start_date) = ? THEN DATEDIFF(l.end_date, l.start_date) + 1 ELSE 0 END) as total_leave_days,
                COUNT(CASE WHEN l.status = 'pending' AND YEAR(l.start_date) = ? THEN 1 END) as pending_leaves
            FROM employees e
            LEFT JOIN leaves l ON e.id = l.employee_id
            WHERE e.company_id = ?
            GROUP BY e.id
            ORDER BY total_leave_days DESC
        ");

        $stmt->execute([$year, $year, $year, $companyId]);
        return $stmt->fetchAll();
    }
}