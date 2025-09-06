<?php

namespace App\Models;

class PayrollRun
{
    public int $id;
    public int $company_id;
    public string $run_date;
    public string $status;
    public float $total_amount;
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