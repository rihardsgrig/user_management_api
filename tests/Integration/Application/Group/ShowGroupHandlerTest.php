<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Group;

use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Handler\Group\GroupResponseBuilder;
use App\Application\Handler\Group\ShowGroupHandler;
use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShowGroupHandlerTest extends KernelTestCase
{
    private GroupRepositoryInterface $groupRepository;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->groupRepository = $container->get(GroupRepositoryInterface::class);

        parent::setUp();
    }

    public function testGetUser(): void
    {
        $group = new Group(
            'project group',
            'some description',
        );
        $this->groupRepository->save($group);

        $groupId = $group->getId();

        $handler = new ShowGroupHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );
        $response = $handler->handle($groupId);

        $this->assertSame($group->title(), $response->data()['title']);
        $this->assertSame($group->description(), $response->data()['description']);
        $this->assertSame($group->createdAt()->format(DateTime::ATOM), $response->data()['created_at']);
    }

    public function testUserIsNotFound(): void
    {
        $handler = new ShowGroupHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Group with id "999" is not found.');
        $handler->handle(999);
    }
}
