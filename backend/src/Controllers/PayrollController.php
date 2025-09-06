<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\PayrollService;
use App\Services\PayslipService;
use App\Repositories\PayrollRunRepository;
use App\Repositories\PayslipRepository;

class PayrollController
{
    private PayrollService $payrollService;
    private PayrollRunRepository $payrollRunRepository;
    private PayslipRepository $payslipRepository;

    public function __construct(
        PayrollService $payrollService,
        PayslipService $payslipService,
        PayrollRunRepository $payrollRunRepository,
        PayslipRepository $payslipRepository
    ) {
        $this->payrollService = $payrollService;
        $this->payslipService = $payslipService;
        $this->payrollRunRepository = $payrollRunRepository;
        $this->payslipRepository = $payslipRepository;
    }

    public function getPayrollRuns(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $companyId = $user['company_id'] ?? 1;

        $queryParams = $request->getQueryParams();
        $limit = (int) ($queryParams['limit'] ?? 10);
        $offset = (int) ($queryParams['offset'] ?? 0);

        $payrollRuns = $this->payrollRunRepository->findAll($companyId, $limit, $offset);

        // Add employee count to each payroll run
        foreach ($payrollRuns as $run) {
            $run->employee_count = $this->payrollRunRepository->getEmployeeCount($run->id);
        }

        $response->getBody()->write(json_encode(array_map(fn($run) => $run->toArray(), $payrollRuns)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPayrollRun(Request $request, Response $response, array $args): Response
    {
        $payrollRun = $this->payrollRunRepository->findById($args['id']);
        if (!$payrollRun) {
            $response->getBody()->write(json_encode(['error' => 'Payroll run not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($payrollRun->toArray()));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function runPayroll(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $user = $request->getAttribute('user');
        $companyId = $user['company_id'] ?? 1;

        try {
            $payrollRun = $this->payrollService->runPayroll(
                $companyId,
                $data['run_date'],
                $data['period'] ?? 'monthly'
            );

            $response->getBody()->write(json_encode($payrollRun->toArray()));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to run payroll: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getPayrollStats(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $companyId = $user['company_id'] ?? 1;

        $stats = $this->payrollService->getPayrollStats($companyId);
        $response->getBody()->write(json_encode($stats));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPayslips(Request $request, Response $response, array $args): Response
    {
        $employeeId = $args['employeeId'] ?? null;
        $payrollRunId = $args['payrollRunId'] ?? null;

        $queryParams = $request->getQueryParams();
        $limit = (int) ($queryParams['limit'] ?? 10);
        $offset = (int) ($queryParams['offset'] ?? 0);

        $payslips = $this->payslipRepository->findAll($employeeId, $payrollRunId, $limit, $offset);
        $response->getBody()->write(json_encode(array_map(fn($payslip) => $payslip->toArray(), $payslips)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPayslipsByRun(Request $request, Response $response, array $args): Response
    {
        $payrollRunId = $args['id'];
        $payslips = $this->payrollService->getPayslipsByRun($payrollRunId);
        $response->getBody()->write(json_encode(array_map(fn($payslip) => $payslip->toArray(), $payslips)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deletePayrollRun(Request $request, Response $response, array $args): Response
    {
        $payrollRunId = $args['id'];

        try {
            // Delete associated payslips first
            $this->payslipRepository->deleteByPayrollRun($payrollRunId);

            // Delete payroll run
            $success = $this->payrollRunRepository->delete($payrollRunId);

            if ($success) {
                $response->getBody()->write(json_encode(['message' => 'Payroll run deleted successfully']));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(['error' => 'Failed to delete payroll run']));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to delete payroll run: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function downloadPayslips(Request $request, Response $response, array $args): Response
    {
        $payrollRunId = $args['id'];
        $format = $request->getQueryParams()['format'] ?? 'csv';

        try {
            if ($format === 'pdf') {
                $pdfContent = $this->payslipService->generateBulkPayslipsPDF($payrollRunId);
                $response->getBody()->write($pdfContent);
                return $response
                    ->withHeader('Content-Type', 'application/pdf')
                    ->withHeader('Content-Disposition', 'attachment; filename="bulk_payslips_' . date('Y-m-d') . '.pdf"');
            } else {
                // CSV format (original implementation)
                $payslips = $this->payrollService->getPayslipsByRun($payrollRunId);

                if (empty($payslips)) {
                    $response->getBody()->write(json_encode(['error' => 'No payslips found for this payroll run']));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                // Create CSV content
                $headers = ['Employee Name', 'Email', 'Gross Pay', 'Net Pay', 'Deductions', 'Allowances'];
                $csvContent = [
                    implode(',', $headers),
                    ...array_map(function($payslip) {
                        return implode(',', [
                            '"' . $payslip->employee_name . '"',
                            '"' . $payslip->employee_email . '"',
                            $payslip->gross_pay,
                            $payslip->net_pay,
                            $payslip->deductions_total,
                            $payslip->allowances_total
                        ]);
                    }, $payslips)
                ];
                $csvContent = implode("\n", $csvContent);

                $response->getBody()->write($csvContent);
                return $response
                    ->withHeader('Content-Type', 'text/csv')
                    ->withHeader('Content-Disposition', 'attachment; filename="payslips_' . $payrollRunId . '.csv"');
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to generate payslips: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function downloadIndividualPayslip(Request $request, Response $response, array $args): Response
    {
        $payslipId = $args['payslipId'];

        try {
            $pdfContent = $this->payslipService->generatePayslipPDF($payslipId);
            $response->getBody()->write($pdfContent);
            return $response
                ->withHeader('Content-Type', 'application/pdf')
                ->withHeader('Content-Disposition', 'attachment; filename="payslip_' . $payslipId . '_' . date('Y-m-d') . '.pdf"');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to generate payslip: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}