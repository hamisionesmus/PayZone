<?php

namespace App\Models;

class Employee
{
    public int $id;
    public int $company_id;
    public ?int $user_id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public ?string $phone;
    public string $hire_date;
    public float $salary;
    public ?string $position;
    public ?string $department;
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

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}