<?php

namespace App\Services;

use App\Repositories\PayrollRunRepository;
use App\Repositories\PayslipRepository;
use App\Repositories\EmployeeRepository;
use App\Models\PayrollRun;
use App\Models\Payslip;

class PayrollService
{
    private PayrollRunRepository $payrollRunRepository;
    private PayslipRepository $payslipRepository;
    private EmployeeRepository $employeeRepository;

    public function __construct(
        PayrollRunRepository $payrollRunRepository,
        PayslipRepository $payslipRepository,
        EmployeeRepository $employeeRepository
    ) {
        $this->payrollRunRepository = $payrollRunRepository;
        $this->payslipRepository = $payslipRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function runPayroll(int $companyId, string $runDate, string $period = 'monthly'): PayrollRun
    {
        // Check if payroll already exists for this date and company
        $existingRuns = $this->payrollRunRepository->findAll($companyId, 1000, 0);
        foreach ($existingRuns as $run) {
            if ($run->run_date === $runDate && in_array($run->status, ['completed', 'processing'])) {
                throw new \Exception('Payroll has already been run for this date');
            }
        }

        // Create payroll run
        $payrollRun = $this->payrollRunRepository->create([
            'company_id' => $companyId,
            'run_date' => $runDate,
            'status' => 'processing'
        ]);

        // Process payroll in background (for now, process immediately)
        $this->processPayrollRun($payrollRun->id, $companyId);

        return $payrollRun;
    }

    private function processPayrollRun(int $payrollRunId, int $companyId): void
    {
        try {
            // Get all active employees for the company
            $employees = $this->employeeRepository->findAll($companyId, 1000, 0); // Get up to 1000 employees

            $totalAmount = 0;
            $processedCount = 0;

            foreach ($employees as $employee) {
                // Calculate payroll for this employee
                $payrollData = $this->calculateEmployeePayroll($employee->id);

                if ($payrollData) {
                    // Create payslip
                    $this->payslipRepository->create([
                        'employee_id' => $employee->id,
                        'payroll_run_id' => $payrollRunId,
                        'gross_pay' => $payrollData['gross_pay'],
                        'net_pay' => $payrollData['net_pay'],
                        'deductions_total' => $payrollData['deductions_total'],
                        'allowances_total' => $payrollData['allowances_total']
                    ]);

                    $totalAmount += $payrollData['net_pay'];
                    $processedCount++;
                }
            }

            // Update payroll run status
            $this->payrollRunRepository->update($payrollRunId, [
                'status' => 'completed',
                'total_amount' => $totalAmount
            ]);

        } catch (\Exception $e) {
            // Update payroll run status to failed
            $this->payrollRunRepository->update($payrollRunId, [
                'status' => 'failed'
            ]);
            throw $e;
        }
    }

    private function calculateEmployeePayroll(int $employeeId): ?array
    {
        $employee = $this->employeeRepository->findById($employeeId);
        if (!$employee) {
            return null;
        }

        // Basic calculation (can be enhanced with deductions/allowances tables)
        $grossPay = $employee->salary;
        $deductionsTotal = 0; // TODO: Calculate from deductions table
        $allowancesTotal = 0; // TODO: Calculate from allowances table

        $netPay = $grossPay + $allowancesTotal - $deductionsTotal;

        return [
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'deductions_total' => $deductionsTotal,
            'allowances_total' => $allowancesTotal
        ];
    }

    public function getPayrollStats(int $companyId): array
    {
        $stats = $this->payrollRunRepository->getStats($companyId);

        // Get total employees paid (from completed runs)
        $completedRuns = $this->payrollRunRepository->findAll($companyId, 1000, 0);
        $employeesPaid = 0;

        foreach ($completedRuns as $run) {
            if ($run->status === 'completed') {
                $employeesPaid += $this->payrollRunRepository->getEmployeeCount($run->id);
            }
        }

        return [
            'total_runs' => $stats['total_runs'] ?? 0,
            'total_amount' => $stats['total_amount'] ?? 0,
            'employees_paid' => $employeesPaid,
            'pending_runs' => $stats['pending_runs'] ?? 0
        ];
    }

    public function getPayslipsByRun(int $payrollRunId): array
    {
        return $this->payslipRepository->getByPayrollRun($payrollRunId);
    }

    public function generatePayslipPDF(int $payslipId): string
    {
        // TODO: Implement PDF generation
        // For now, return a placeholder
        return "PDF generation not yet implemented";
    }
}