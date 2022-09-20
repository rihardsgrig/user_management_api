<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Handler\Exceptions\UserAreadyExistsException;
use App\Application\Handler\User\Dto\CreateUserRequest;
use App\Application\Handler\User\Dto\UserResponse;
use App\Domain\User\Exception\InvalidInputDataException;
use App\Domain\User\UniqueEmailSpecificationInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;

class CreateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UniqueEmailSpecificationInterface $uniqueEmailSpecification
    ) {
    }

    public function handle(CreateUserRequest $request): UserResponse
    {
        try {
            $user = new User(
                $request->firstName(),
                $request->lastName(),
                $request->email(),
                $this->uniqueEmailSpecification
            );
        } catch (InvalidInputDataException $e) {
            throw new UserAreadyExistsException($e->getMessage(), $e);
        }

        $this->userRepository->save($user);

        return UserResponse::createFromUser($user);
    }
}
