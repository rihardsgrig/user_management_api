<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\User;

use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Application\Handler\User\ShowUserHandler;
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
        );
        $response = $handler->handle($userId);

        $this->assertEquals($user->firstName(), $response->toArray()['first_name']);
        $this->assertEquals($user->lastName(), $response->toArray()['last_name']);
        $this->assertEquals($user->email(), $response->toArray()['email']);
        $this->assertFalse($response->toArray()['is_admin']);
        $this->assertEquals($user->createdAt()->format(DateTime::ATOM), $response->toArray()['created_at']);
    }

    public function testUserIsNotFound(): void
    {
        $handler = new ShowUserHandler(
            $this->userRepository,
        );

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('User with id "999" is not found.');
        $handler->handle(999);
    }
}
