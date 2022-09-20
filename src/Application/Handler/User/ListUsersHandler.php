<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use DateTime;

class ListUsersHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return array<int, array{id: int, first_name: string, last_name: string, email: string, is_admin: bool, created_at: string}>
     */
    public function handle(int $offset = 0, int $limit = 10): array
    {
        return $this->userRepository->all($offset, $limit)->map(
            static function (User $user) {
                return [
                    'id' => $user->getId(),
                    'first_name' => $user->firstName(),
                    'last_name' => $user->lastName(),
                    'email' => $user->email(),
                    'is_admin' => $user->isAdmin(),
                    'created_at' => $user->createdAt()->format(DateTime::ATOM),
                ];
            }
        )->toArray();
    }
}
