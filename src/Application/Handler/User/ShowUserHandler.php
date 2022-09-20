<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Application\Handler\User\Dto\UserResponse;
use App\Domain\User\UserRepositoryInterface;

class ShowUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(int $id): UserResponse
    {
        $user = $this->userRepository->find($id);

        if ($user === null) {
            throw new ResourceNotFoundException(sprintf('User with id "%s" is not found.', $id));
        }

        return UserResponse::createFromUser($user);
    }
}
