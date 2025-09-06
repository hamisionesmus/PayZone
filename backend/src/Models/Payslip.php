<?php

namespace App\Models;

class Payslip
{
    public int $id;
    public int $employee_id;
    public int $payroll_run_id;
    public float $gross_pay;
    public float $net_pay;
    public float $deductions_total;
    public float $allowances_total;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}