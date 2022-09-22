<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Response\ResponseInterface;
use App\Domain\User\UserRepositoryInterface;

class ListUsersHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserResponseBuilder $responseBuilder
    ) {
    }

    public function handle(int $offset = 0, int $limit = 10): ResponseInterface
    {
        return $this->responseBuilder->buildCollection(
            $this->userRepository->all($offset, $limit)
        );
    }
}
