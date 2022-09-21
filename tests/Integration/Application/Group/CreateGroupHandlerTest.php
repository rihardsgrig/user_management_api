<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Group;

use App\Application\Handler\Group\CreateGroupHandler;
use App\Application\Handler\Group\Dto\CreateGroupRequest;
use App\Domain\Group\GroupRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateGroupHandlerTest extends KernelTestCase
{
    private GroupRepositoryInterface $groupRepository;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->groupRepository = $container->get(GroupRepositoryInterface::class);

        parent::setUp();
    }

    public function testCreateGroup(): void
    {
        $handler = new CreateGroupHandler(
            $this->groupRepository,
        );

        $request = new CreateGroupRequest();
        $request->title = 'project group';
        $request->description = 'some description';

        $response = $handler->handle($request);

        $this->assertContains('project group', $response->toArray());
        $this->assertContains('some description', $response->toArray());
    }
}
