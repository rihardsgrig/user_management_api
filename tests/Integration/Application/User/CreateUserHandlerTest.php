<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\User;

use App\Application\Handler\Exception\InvalidUserEmailException;
use App\Application\Handler\Exception\UserAreadyExistsException;
use App\Application\Handler\User\CreateUserHandler;
use App\Application\Handler\User\Dto\CreateUserRequest;
use App\Application\Handler\User\UserResponseBuilder;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Specification\UniqueEmailSpecification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateUserHandlerTest extends KernelTestCase
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

    public function testCreateUser(): void
    {
        $handler = new CreateUserHandler(
            $this->userRepository,
            $this->uniqueEmailSpecification,
            new UserResponseBuilder()
        );

        $request = new CreateUserRequest();
        $request->firstName = 'John';
        $request->lastName = 'Doe';
        $request->email = 'test@test.com';

        $response = $handler->handle($request);

        $this->assertContains('John', $response->data());
        $this->assertContains('Doe', $response->data());
        $this->assertContains('test@test.com', $response->data());
        $this->assertContains('test@test.com', $response->data());
    }

    public function testUserIsNotCreatedWithInvalidEmail(): void
    {
        $handler = new CreateUserHandler(
            $this->userRepository,
            $this->uniqueEmailSpecification,
            new UserResponseBuilder()
        );

        $request = new CreateUserRequest();
        $request->firstName = 'John';
        $request->lastName = 'Doe';
        $request->email = 'INVALID_EMAIL';

        $this->expectException(InvalidUserEmailException::class);
        $this->expectExceptionMessage('Invalid email "INVALID_EMAIL".');
        $handler->handle($request);
    }

    public function testIsUserNotCreatedTwice(): void
    {
        $user = new User(
            'John',
            'Doe',
            'test@test.com',
            $this->uniqueEmailSpecification
        );

        $this->userRepository->save($user);

        $handler = new CreateUserHandler(
            $this->userRepository,
            $this->uniqueEmailSpecification,
            new UserResponseBuilder()
        );

        $request = new CreateUserRequest();
        $request->firstName = 'Jane';
        $request->lastName = 'Doe';
        $request->email = 'test@test.com';

        $this->expectException(UserAreadyExistsException::class);
        $this->expectExceptionMessage('User with email "test@test.com" already exist.');
        $handler->handle($request);
    }
}
