<?php

namespace App\Models;

class Leave
{
    public int $id = 0;
    public int $employee_id = 0;
    public string $start_date = '';
    public string $end_date = '';
    public string $type = '';
    public string $status = 'pending';
    public ?string $reason = null;
    public string $created_at = '';
    public string $updated_at = '';

    // Joined employee properties
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $employee_email = null;

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