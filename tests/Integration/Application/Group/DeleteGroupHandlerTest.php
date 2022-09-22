<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Group;

use App\Application\Handler\Exception\MembersAttachedToGroupException;
use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Handler\Group\DeleteGroupHandler;
use App\Application\Handler\Group\GroupResponseBuilder;
use App\Application\Response\EmptyResponse;
use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Specification\UniqueEmailSpecification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteGroupHandlerTest extends KernelTestCase
{
    private groupRepositoryInterface $groupRepository;
    private UniqueEmailSpecification $uniqueEmailSpecification;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->groupRepository = $container->get(GroupRepositoryInterface::class);
        $this->uniqueEmailSpecification = $container->get(UniqueEmailSpecification::class);

        parent::setUp();
    }

    public function testDeleteGroup(): void
    {
        $group = new Group(
            'project group',
            'some description',
        );
        $this->groupRepository->save($group);

        $groupId = $group->getId();

        $handler = new DeleteGroupHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );

        $response = $handler->handle($groupId);
        $this->assertInstanceOf(EmptyResponse::class, $response);

        // test group does not exist anymore
        $this->assertNull($this->groupRepository->find($groupId));
    }

    public function testGroupIsNotFound(): void
    {
        $handler = new DeleteGroupHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Group with id "999" is not found.');
        $handler->handle(999);
    }

    public function testCanNotDeleteGroupWithMembers(): void
    {
        $user = new User(
            'John',
            'Doe',
            'test@test.com',
            $this->uniqueEmailSpecification
        );

        $group = new Group(
            'project group',
            'some description',
        );

        $group->addMembership($user);
        $this->groupRepository->save($group);

        $groupId = $group->getId();
        $handler = new DeleteGroupHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );

        $this->expectException(MembersAttachedToGroupException::class);
        $this->expectExceptionMessage(sprintf('Group with id "%s" has members.', $groupId));
        $handler->handle($groupId);
    }
}
