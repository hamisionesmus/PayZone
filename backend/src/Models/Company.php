<?php

namespace App\Models;

class Company
{
    public int $id;
    public string $name;
    public ?string $address;
    public ?string $phone;
    public ?string $email;
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