<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Handler\Exception\InvalidUserEmailException as InvalidEmailException;
use App\Application\Handler\Exception\UserAreadyExistsException;
use App\Application\Handler\User\Dto\CreateUserRequest;
use App\Application\Response\ResponseInterface;
use App\Domain\User\Exception\InvalidInputDataException;
use App\Domain\User\Exception\InvalidUserEmailException;
use App\Domain\User\UniqueEmailSpecificationInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;

class CreateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UniqueEmailSpecificationInterface $uniqueEmailSpecification,
        private readonly UserResponseBuilder $responseBuilder
    ) {
    }

    public function handle(CreateUserRequest $request): ResponseInterface
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
        } catch (InvalidUserEmailException $e) {
            throw new InvalidEmailException($e->getMessage(), $e);
        }

        $this->userRepository->save($user);

        return $this->responseBuilder->buildItem($user);
    }
}
