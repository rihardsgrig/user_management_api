<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Group;

use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Application\Handler\Exceptions\UserIsMemberException;
use App\Application\Handler\Exceptions\UserMissingException;
use App\Application\Handler\Group\AddMemberHandler;
use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Specification\UniqueEmailSpecification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddMemberHandlerTest extends KernelTestCase
{
    private GroupRepositoryInterface $groupRepository;
    private UserRepositoryInterface $userRepository;
    private UniqueEmailSpecification $uniqueEmailSpecification;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->groupRepository = $container->get(GroupRepositoryInterface::class);
        $this->userRepository = $container->get(UserRepositoryInterface::class);
        $this->uniqueEmailSpecification = $container->get(UniqueEmailSpecification::class);

        parent::setUp();
    }

    public function testGroupIsNotFound(): void
    {
        $handler = new AddMemberHandler(
            $this->groupRepository,
            $this->userRepository
        );

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Group with id "999" is not found.');
        $handler->handle(999, 111);
    }

    public function testUserIsNotFound(): void
    {
        $group = new Group(
            'project group',
            'some description',
        );
        $this->groupRepository->save($group);

        $groupId = $group->getId();

        $handler = new AddMemberHandler(
            $this->groupRepository,
            $this->userRepository
        );

        $this->expectException(UserMissingException::class);
        $this->expectExceptionMessage('User with id "111" is not found.');
        $handler->handle($groupId, 111);
    }

    public function testCantAddMemberTwice(): void
    {
        $group = new Group(
            'project group',
            'some description',
        );

        $user = new User(
            'John',
            'Doe',
            'test@test.com',
            $this->uniqueEmailSpecification
        );

        $group->addMembership($user);

        $this->groupRepository->save($group);

        $groupId = $group->getId();
        $userId = $user->getId();

        $handler = new AddMemberHandler(
            $this->groupRepository,
            $this->userRepository
        );

        $this->expectException(UserIsMemberException::class);
        $this->expectExceptionMessage(sprintf(
            'User with id "%s" is already member of the group "%s".',
            $userId,
            $groupId
        ));
        $handler->handle($groupId, $userId);
    }

    public function testAddUserAsMemberToGroup(): void
    {
        $group = new Group(
            'project group',
            'some description',
        );
        $this->groupRepository->save($group);

        $user = new User(
            'John',
            'Doe',
            'test@test.com',
            $this->uniqueEmailSpecification
        );
        $this->userRepository->save($user);

        $groupId = $group->getId();
        $userId = $user->getId();

        $handler = new AddMemberHandler(
            $this->groupRepository,
            $this->userRepository
        );

        $handler->handle($groupId, $userId);
        $this->assertTrue($group->hasMember($user));
    }
}
