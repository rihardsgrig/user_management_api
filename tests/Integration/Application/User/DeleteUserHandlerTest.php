<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\User;

use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Application\Handler\User\DeleteUserHandler;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Specification\UniqueEmailSpecification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteUserHandlerTest extends KernelTestCase
{
    private UserRepositoryInterface $userRepository;
    private UniqueEmailSpecification $uniqueEmailSpecification;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->userRepository = $container->get(UserRepositoryInterface::class);
        $this->uniqueEmailSpecification = $container->get(UniqueEmailSpecification::class);

        parent::setUp();
    }

    public function testDeleteUser(): void
    {
        $user = new User(
            'John',
            'Doe',
            'test@test.com',
            $this->uniqueEmailSpecification
        );
        $this->userRepository->save($user);

        $userId = $user->getId();

        $handler = new DeleteUserHandler(
            $this->userRepository,
        );
        $handler->handle($userId);

        // test user does not exist anymore
        $this->assertNull($this->userRepository->find($userId));
    }

    public function testUserIsNotFound(): void
    {
        $handler = new DeleteUserHandler(
            $this->userRepository,
        );

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('User with id "999" is not found.');
        $handler->handle(999);
    }
}
