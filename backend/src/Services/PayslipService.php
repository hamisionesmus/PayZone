<?php

namespace App\Services;

use App\Repositories\PayslipRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\PayrollRunRepository;

class PayslipService
{
    private PayslipRepository $payslipRepository;
    private EmployeeRepository $employeeRepository;
    private PayrollRunRepository $payrollRunRepository;

    public function __construct(
        PayslipRepository $payslipRepository,
        EmployeeRepository $employeeRepository,
        PayrollRunRepository $payrollRunRepository
    ) {
        $this->payslipRepository = $payslipRepository;
        $this->employeeRepository = $employeeRepository;
        $this->payrollRunRepository = $payrollRunRepository;
    }

    public function generatePayslipPDF(int $payslipId): string
    {
        $payslip = $this->payslipRepository->findById($payslipId);
        if (!$payslip) {
            throw new \Exception('Payslip not found');
        }

        $employee = $this->employeeRepository->findById($payslip->employee_id);
        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        $payrollRun = $this->payrollRunRepository->findById($payslip->payroll_run_id);
        if (!$payrollRun) {
            throw new \Exception('Payroll run not found');
        }

        // Create a simple PDF using basic PHP
        // This creates a minimal PDF structure that should be readable by PDF viewers
        $content = $this->createSimplePDF($employee, $payslip, $payrollRun);
        return $content;
    }

    private function createSimplePDF($employee, $payslip, $payrollRun): string
    {
        // Create a basic PDF structure
        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Catalog\n";
        $pdf .= "/Pages 2 0 R\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";

        $pdf .= "2 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Pages\n";
        $pdf .= "/Kids [3 0 R]\n";
        $pdf .= "/Count 1\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";

        // Page content
        $content = "BT\n";
        $content .= "/F1 12 Tf\n";
        $content .= "50 750 Td\n";
        $content .= "(TECHCORP LIMITED) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(P.O. Box 12345-00100, Nairobi, Kenya) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(Tel: +254 20 123 4567 | Email: payroll@techcorp.co.ke) Tj\n";
        $content .= "0 -40 Td\n";
        $content .= "(EMPLOYEE PAYSLIP) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(Pay Period: " . date('F Y', strtotime($payrollRun->run_date)) . ") Tj\n";
        $content .= "0 -40 Td\n";
        $content .= "(EMPLOYEE INFORMATION) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(Employee Name: " . $employee->first_name . ' ' . $employee->last_name . ") Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(Employee ID: " . str_pad($employee->id, 6, '0', STR_PAD_LEFT) . ") Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(KRA PIN: A" . str_pad($employee->id, 9, '0', STR_PAD_LEFT) . "Z) Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(Position: " . ($employee->position ?? 'N/A') . ") Tj\n";
        $content .= "0 -40 Td\n";
        $content .= "(SALARY INFORMATION) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(Basic Salary: KSh " . number_format($employee->salary, 2) . ") Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(Gross Pay: KSh " . number_format($payslip->gross_pay, 2) . ") Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(Total Deductions: KSh " . number_format($payslip->deductions_total, 2) . ") Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(NET PAY: KSh " . number_format($payslip->net_pay, 2) . ") Tj\n";
        $content .= "0 -40 Td\n";
        $content .= "(PAYMENT DETAILS) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(Payment Date: " . date('d/m/Y', strtotime($payrollRun->run_date)) . ") Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(Payment Method: Bank Transfer) Tj\n";
        $content .= "0 -15 Td\n";
        $content .= "(Bank: Kenya Commercial Bank) Tj\n";
        $content .= "0 -40 Td\n";
        $content .= "(Generated on: " . date('d/m/Y H:i:s') . ") Tj\n";
        $content .= "ET\n";

        $pdf .= "3 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Page\n";
        $pdf .= "/Parent 2 0 R\n";
        $pdf .= "/MediaBox [0 0 612 792]\n";
        $pdf .= "/Contents 4 0 R\n";
        $pdf .= "/Resources << /Font << /F1 5 0 R >> >>\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";

        $pdf .= "4 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Length " . strlen($content) . "\n";
        $pdf .= ">>\n";
        $pdf .= "stream\n";
        $pdf .= $content;
        $pdf .= "endstream\n";
        $pdf .= "endobj\n";

        $pdf .= "5 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Font\n";
        $pdf .= "/Subtype /Type1\n";
        $pdf .= "/BaseFont /Helvetica\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";

        $pdf .= "xref\n";
        $pdf .= "0 6\n";
        $pdf .= "0000000000 65535 f \n";
        $pdf .= "0000000009 00000 n \n";
        $pdf .= "0000000058 00000 n \n";
        $pdf .= "0000000115 00000 n \n";
        $pdf .= "0000000274 00000 n \n";
        $pdf .= "0000001000 00000 n \n";

        $pdf .= "trailer\n";
        $pdf .= "<<\n";
        $pdf .= "/Size 6\n";
        $pdf .= "/Root 1 0 R\n";
        $pdf .= ">>\n";
        $pdf .= "startxref\n";
        $pdf .= "1200\n";
        $pdf .= "%%EOF\n";

        return $pdf;
    }

    private function generatePayslipHTML($employee, $payslip, $payrollRun): string
    {
        $html = '
<!DOCTYPE html>
<html>
<head>
    <title>Payslip - ' . htmlspecialchars($employee->first_name . ' ' . $employee->last_name) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .company-info { font-size: 12px; color: #666; }
        .payslip-title { font-size: 20px; font-weight: bold; text-align: center; margin: 20px 0; }
        .section { margin-bottom: 20px; }
        .section-title { background: #f8f9fa; padding: 8px; font-weight: bold; border: 1px solid #dee2e6; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .info-table td { padding: 6px; border: 1px solid #dee2e6; }
        .info-table .label { font-weight: bold; width: 150px; background: #f8f9fa; }
        .net-pay { background: #28a745; color: white; font-weight: bold; font-size: 16px; }
        .footer { text-align: center; font-size: 10px; color: #666; margin-top: 40px; border-top: 1px solid #dee2e6; padding-top: 20px; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">TECHCORP PAYROLL MANAGEMENT SYSTEM</div>
        <div class="company-info">
            123 Tech Street, Tech City, TC 12345<br>
            Phone: (555) 123-4567 | Email: payroll@techcorp.com
        </div>
    </div>

    <div class="payslip-title">EMPLOYEE PAYSLIP</div>
    <div style="text-align: center; margin-bottom: 20px;">
        Pay Period: ' . date('F Y', strtotime($payrollRun->run_date)) . '
    </div>

    <div class="section">
        <div class="section-title">EMPLOYEE INFORMATION</div>
        <table class="info-table">
            <tr><td class="label">Employee Name:</td><td>' . htmlspecialchars($employee->first_name . ' ' . $employee->last_name) . '</td></tr>
            <tr><td class="label">Employee ID:</td><td>' . str_pad($employee->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
            <tr><td class="label">Position:</td><td>' . htmlspecialchars($employee->position ?? 'N/A') . '</td></tr>
            <tr><td class="label">Department:</td><td>' . htmlspecialchars($employee->department ?? 'N/A') . '</td></tr>
            <tr><td class="label">Hire Date:</td><td>' . date('M d, Y', strtotime($employee->hire_date)) . '</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">SALARY INFORMATION</div>
        <table class="info-table">
            <tr><td class="label">Basic Salary:</td><td>$' . number_format($employee->salary, 2) . '</td></tr>
            <tr><td class="label">Gross Pay:</td><td>$' . number_format($payslip->gross_pay, 2) . '</td></tr>
            <tr><td class="label">Total Deductions:</td><td>$' . number_format($payslip->deductions_total, 2) . '</td></tr>
            <tr><td class="label">Total Allowances:</td><td>$' . number_format($payslip->allowances_total, 2) . '</td></tr>
            <tr><td class="label net-pay">NET PAY:</td><td class="net-pay">$' . number_format($payslip->net_pay, 2) . '</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">PAYMENT DETAILS</div>
        <table class="info-table">
            <tr><td class="label">Payment Date:</td><td>' . date('M d, Y', strtotime($payrollRun->run_date)) . '</td></tr>
            <tr><td class="label">Payment Method:</td><td>Direct Deposit</td></tr>
            <tr><td class="label">Payroll Run ID:</td><td>' . str_pad($payrollRun->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
        </table>
    </div>

    <div class="footer">
        This is a computer-generated payslip and does not require a signature.<br>
        Generated on: ' . date('M d, Y H:i:s') . '
    </div>
</body>
</html>';

        return $html;
    }

    public function generateBulkPayslipsPDF(int $payrollRunId): string
    {
        $payslips = $this->payslipRepository->getByPayrollRun($payrollRunId);
        $payrollRun = $this->payrollRunRepository->findById($payrollRunId);

        if (empty($payslips)) {
            throw new \Exception('No payslips found for this payroll run');
        }

        // For bulk, create a simple text-based PDF with all employees
        $content = "%PDF-1.4\n";
        $content .= "1 0 obj\n";
        $content .= "<<\n";
        $content .= "/Type /Catalog\n";
        $content .= "/Pages 2 0 R\n";
        $content .= ">>\n";
        $content .= "endobj\n";

        $content .= "2 0 obj\n";
        $content .= "<<\n";
        $content .= "/Type /Pages\n";
        $content .= "/Kids [";

        $pageCount = count($payslips);
        for ($i = 0; $i < $pageCount; $i++) {
            $content .= ($i + 3) . " 0 R ";
        }
        $content .= "]\n";
        $content .= "/Count $pageCount\n";
        $content .= ">>\n";
        $content .= "endobj\n";

        $xref = "xref\n0 " . ($pageCount + 4) . "\n0000000000 65535 f \n";

        $objectNumber = 3;
        $position = 200;

        foreach ($payslips as $index => $payslip) {
            $employee = $this->employeeRepository->findById($payslip->employee_id);

            $pageContent = "BT\n";
            $pageContent .= "/F1 12 Tf\n";
            $pageContent .= "50 750 Td\n";
            $pageContent .= "(TECHCORP LIMITED - BULK PAYSLIPS) Tj\n";
            $pageContent .= "0 -30 Td\n";
            $pageContent .= "(Pay Period: " . date('F Y', strtotime($payrollRun->run_date)) . ") Tj\n";
            $pageContent .= "0 -30 Td\n";
            $pageContent .= "(" . $employee->first_name . ' ' . $employee->last_name . ") Tj\n";
            $pageContent .= "0 -20 Td\n";
            $pageContent .= "(Employee ID: " . str_pad($employee->id, 6, '0', STR_PAD_LEFT) . ") Tj\n";
            $pageContent .= "0 -20 Td\n";
            $pageContent .= "(Basic Salary: KSH " . number_format($employee->salary, 2) . ") Tj\n";
            $pageContent .= "0 -15 Td\n";
            $pageContent .= "(Gross Pay: KSH " . number_format($payslip->gross_pay, 2) . ") Tj\n";
            $pageContent .= "0 -15 Td\n";
            $pageContent .= "(Deductions: KSH " . number_format($payslip->deductions_total, 2) . ") Tj\n";
            $pageContent .= "0 -15 Td\n";
            $pageContent .= "(NET PAY: KSH " . number_format($payslip->net_pay, 2) . ") Tj\n";
            $pageContent .= "ET\n";

            $content .= "$objectNumber 0 obj\n";
            $content .= "<<\n";
            $content .= "/Type /Page\n";
            $content .= "/Parent 2 0 R\n";
            $content .= "/MediaBox [0 0 612 792]\n";
            $content .= "/Contents " . ($objectNumber + $pageCount) . " 0 R\n";
            $content .= "/Resources << /Font << /F1 " . ($objectNumber + 2 * $pageCount) . " 0 R >> >>\n";
            $content .= ">>\n";
            $content .= "endobj\n";

            $content .= ($objectNumber + $pageCount) . " 0 obj\n";
            $content .= "<<\n";
            $content .= "/Length " . strlen($pageContent) . "\n";
            $content .= ">>\n";
            $content .= "stream\n";
            $content .= $pageContent;
            $content .= "endstream\n";
            $content .= "endobj\n";

            $xref .= sprintf("%010d 00000 n \n", $position);
            $xref .= sprintf("%010d 00000 n \n", $position + 100);

            $objectNumber++;
        }

        $content .= ($objectNumber + $pageCount) . " 0 obj\n";
        $content .= "<<\n";
        $content .= "/Type /Font\n";
        $content .= "/Subtype /Type1\n";
        $content .= "/BaseFont /Helvetica\n";
        $content .= ">>\n";
        $content .= "endobj\n";

        $content .= $xref;
        $content .= "trailer\n";
        $content .= "<<\n";
        $content .= "/Size " . ($pageCount * 2 + 4) . "\n";
        $content .= "/Root 1 0 R\n";
        $content .= ">>\n";
        $content .= "startxref\n";
        $content .= "1500\n";
        $content .= "%%EOF\n";

        return $content;
    }
}