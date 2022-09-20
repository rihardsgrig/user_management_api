<?php

declare(strict_types=1);

namespace App\Application\Handler\User\Dto;

class CreateUserRequest
{
    public string $firstName;
    public string $lastName;
    public string $email;

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }
}
