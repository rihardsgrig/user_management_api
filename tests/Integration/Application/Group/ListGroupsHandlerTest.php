<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Group;

use App\Application\Handler\Group\GroupResponseBuilder;
use App\Application\Handler\Group\ListGroupsHandler;
use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ListGroupsHandlerTest extends KernelTestCase
{
    private GroupRepositoryInterface $groupRepository;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->groupRepository = $container->get(GroupRepositoryInterface::class);

        parent::setUp();
    }

    public function testReturnsEmptyGroupsList(): void
    {
        $handler = new ListGroupsHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );
        $response = $handler->handle();

        $this->assertEmpty($response->data());
    }

    public function testReturnsGroupList(): void
    {
        $this->seeder(10);

        $handler = new ListGroupsHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );
        $response = $handler->handle();

        $this->assertCount(10, $response->data());
    }

    public function testPaginatesGroupList(): void
    {
        $this->seeder(3);
        $handler = new ListGroupsHandler(
            $this->groupRepository,
            new GroupResponseBuilder()
        );

        $response = $handler->handle(0, 2);

        $this->assertCount(2, $response->data());
        $this->assertSame('Group 1', $response->data()[0]['title']);
        $this->assertSame('Group 2', $response->data()[1]['title']);
    }

    private function seeder(int $count): void
    {
        for ($x = 1; $x <= $count; ++$x) {
            $group = new Group(
                sprintf('Group %s', $x),
                'some description',
            );
            $this->groupRepository->save($group);
        }
    }
}
