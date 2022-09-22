<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\User;

use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Handler\User\ShowUserHandler;
use App\Application\Handler\User\UserResponseBuilder;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Specification\UniqueEmailSpecification;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShowUserHandlerTest extends KernelTestCase
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

    public function testGetUser(): void
    {
        $user = new User(
            'John',
            'Doe',
            'test@test.com',
            $this->uniqueEmailSpecification
        );
        $this->userRepository->save($user);

        $userId = $user->getId();

        $handler = new ShowUserHandler(
            $this->userRepository,
            new UserResponseBuilder()
        );
        $response = $handler->handle($userId);

        $this->assertSame($user->firstName(), $response->data()['first_name']);
        $this->assertSame($user->lastName(), $response->data()['last_name']);
        $this->assertSame($user->email(), $response->data()['email']);
        $this->assertFalse($response->data()['is_admin']);
        $this->assertSame($user->createdAt()->format(DateTime::ATOM), $response->data()['created_at']);
    }

    public function testUserIsNotFound(): void
    {
        $handler = new ShowUserHandler(
            $this->userRepository,
            new UserResponseBuilder()
        );

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('User with id "999" is not found.');
        $handler->handle(999);
    }
}
