<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Response\ResponseInterface;
use App\Domain\User\UserRepositoryInterface;

class DeleteUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserResponseBuilder $responseBuilder
    ) {
    }

    public function handle(int $id): ResponseInterface
    {
        $user = $this->userRepository->find($id);

        if ($user === null) {
            throw new ResourceNotFoundException(sprintf('User with id "%s" is not found.', $id));
        }

        $this->userRepository->remove($user);

        return $this->responseBuilder->buildEmpty();
    }
}
