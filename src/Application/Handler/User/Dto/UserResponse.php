<?php

declare(strict_types=1);

namespace App\Application\Handler\User\Dto;

use App\Domain\User\User;
use DateTime;
use DateTimeImmutable;

class UserResponse
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private bool $isAdmin;
    private DateTimeImmutable $createdAt;

    private function __construct()
    {
    }

    /**
     * @return array{
     *     'id': int,
     *     'first_name': string,
     *     'last_name': string,
     *     'email': string,
     *     'is_admin': bool,
     *     'created_at': string
     * }
     */
    public function __toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'is_admin' => $this->isAdmin,
            'created_at' => $this->createdAt->format(DateTime::ATOM),
        ];
    }

    public static function createFromUser(User $user): self
    {
        $object = new self();

        $object->id = $user->getId();
        $object->firstName = $user->firstName();
        $object->lastName = $user->lastName();
        $object->email = $user->email();
        $object->isAdmin = $user->isAdmin();
        $object->createdAt = $user->createdAt();

        return $object;
    }
}
