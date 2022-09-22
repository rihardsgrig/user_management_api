<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Response\ResponseInterface;
use App\Domain\Group\GroupRepositoryInterface;

class ListMembersHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly MemberResponseBuilder $responseBuilder
    ) {
    }

    public function handle(int $groupId): ResponseInterface
    {
        $group = $this->groupRepository->find($groupId);
        if ($group === null) {
            throw new ResourceNotFoundException(sprintf('Group with id "%s" is not found.', $groupId));
        }

        return $this->responseBuilder->buildCollection(
            $group->memberships()
        );
    }
}
