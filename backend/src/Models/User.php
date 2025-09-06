<?php

namespace App\Models;

class User
{
    public int $id;
    public ?int $company_id;
    public string $username;
    public string $email;
    public string $password_hash;
    public int $role_id;
    public bool $is_active;
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