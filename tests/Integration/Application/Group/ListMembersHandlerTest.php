<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Group;

use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Handler\Group\ListMembersHandler;
use App\Application\Handler\Group\MemberResponseBuilder;
use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Specification\UniqueEmailSpecification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ListMembersHandlerTest extends KernelTestCase
{
    private GroupRepositoryInterface $groupRepository;
    private UniqueEmailSpecification $uniqueEmailSpecification;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->groupRepository = $container->get(GroupRepositoryInterface::class);
        $this->uniqueEmailSpecification = $container->get(UniqueEmailSpecification::class);

        parent::setUp();
    }

    public function testGroupIsNotFound(): void
    {
        $handler = new ListMembersHandler(
            $this->groupRepository,
            new MemberResponseBuilder()
        );

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Group with id "999" is not found.');
        $handler->handle(999);
    }

    public function testReturnsEmptyMemberList(): void
    {
        $group = new Group(
            'project group',
            'some description',
        );
        $this->groupRepository->save($group);

        $handler = new ListMembersHandler(
            $this->groupRepository,
            new MemberResponseBuilder()
        );
        $response = $handler->handle($group->getId());

        $this->assertEmpty($response->data());
    }

    public function testReturnsMemberList(): void
    {
        $group = new Group(
            'project group',
            'some description',
        );

        $john = new User(
            'John',
            'Doe',
            'test1@test.com',
            $this->uniqueEmailSpecification
        );

        $jane = new User(
            'Jane',
            'Doe',
            'test2@test.com',
            $this->uniqueEmailSpecification
        );

        $group->addMembership($john);
        $group->addMembership($jane);

        $this->groupRepository->save($group);

        $groupId = $group->getId();
        $johnId = $john->getId();
        $janeId = $jane->getId();

        $handler = new ListMembersHandler(
            $this->groupRepository,
            new MemberResponseBuilder()
        );

        $response = $handler->handle($groupId);

        $this->assertCount(2, $response->data());
        $this->assertSame($johnId, $response->data()[0]['id']);
        $this->assertSame($janeId, $response->data()[1]['id']);
    }
}
