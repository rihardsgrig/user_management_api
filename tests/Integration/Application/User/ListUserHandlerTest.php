<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\User;

use App\Application\Handler\User\ListUsersHandler;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Specification\UniqueEmailSpecification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ListUserHandlerTest extends KernelTestCase
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

    public function testReturnsEmptyUserList(): void
    {
        $handler = new ListUsersHandler(
            $this->userRepository,
        );
        $response = $handler->handle();

        $this->assertEmpty($response);
    }

    public function testReturnsUserList(): void
    {
        $this->seeder(10);

        $handler = new ListUsersHandler(
            $this->userRepository,
        );
        $response = $handler->handle();

        $this->assertCount(10, $response);
    }

    public function testPaginatesUserList(): void
    {
        $this->seeder(3);
        $handler = new ListUsersHandler(
            $this->userRepository,
        );

        $response = $handler->handle(0, 2);

        $this->assertCount(2, $response);
        $this->assertEquals('test1@test.com', $response[0]['email']);
        $this->assertEquals('test2@test.com', $response[1]['email']);
    }

    private function seeder(int $count): void
    {
        for ($x = 1; $x <= $count; ++$x) {
            $user = new User(
                'first_name',
                'last_name',
                sprintf('test%s@test.com', $x),
                $this->uniqueEmailSpecification
            );
            $this->userRepository->save($user);
        }
    }
}
