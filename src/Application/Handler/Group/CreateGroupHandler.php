<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Group\Dto\CreateGroupRequest;
use App\Application\Response\ResponseInterface;
use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;

class CreateGroupHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly GroupResponseBuilder $responseBuilder
    ) {
    }

    public function handle(CreateGroupRequest $request): ResponseInterface
    {
        $group = new Group(
            $request->title(),
            $request->description()
        );

        $this->groupRepository->save($group);

        return $this->responseBuilder->buildItem($group);
    }
}
