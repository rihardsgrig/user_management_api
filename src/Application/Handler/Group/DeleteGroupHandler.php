<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Exception\MembersAttachedToGroupException;
use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Response\ResponseInterface;
use App\Domain\Group\GroupRepositoryInterface;

class DeleteGroupHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly GroupResponseBuilder $responseBuilder
    ) {
    }

    public function handle(int $id): ResponseInterface
    {
        $group = $this->groupRepository->find($id);

        if ($group === null) {
            throw new ResourceNotFoundException(sprintf('Group with id "%s" is not found.', $id));
        }

        if ($group->hasMembers()) {
            throw new MembersAttachedToGroupException(sprintf('Group with id "%s" has members.', $id));
        }

        $this->groupRepository->remove($group);

        return $this->responseBuilder->buildEmpty();
    }
}
