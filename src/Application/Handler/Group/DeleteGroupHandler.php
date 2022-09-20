<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Exceptions\MembersAttachedToGroupException;
use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Domain\Group\GroupRepositoryInterface;

class DeleteGroupHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository
    ) {
    }

    public function handle(int $id): void
    {
        $group = $this->groupRepository->find($id);

        if ($group === null) {
            throw new ResourceNotFoundException(sprintf('Group with id "%s" is not found.', $id));
        }

        if ($group->hasMembers()) {
            throw new MembersAttachedToGroupException(sprintf('Group with id "%s" has members.', $id));
        }

        $this->groupRepository->remove($group);
    }
}
