<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Application\Handler\Group\Dto\GroupResponse;
use App\Domain\Group\GroupRepositoryInterface;

class ShowGroupHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository
    ) {
    }

    public function handle(int $id): GroupResponse
    {
        $group = $this->groupRepository->find($id);

        if ($group === null) {
            throw new ResourceNotFoundException(sprintf('Group with id "%s" is not found.', $id));
        }

        return GroupResponse::createFromGroup($group);
    }
}
