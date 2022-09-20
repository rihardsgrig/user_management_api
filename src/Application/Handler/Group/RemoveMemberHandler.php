<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Application\Handler\Exceptions\UserMissingException;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;

class RemoveMemberHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(int $groupId, int $userId): void
    {
        $group = $this->groupRepository->find($groupId);
        if ($group === null) {
            throw new ResourceNotFoundException(sprintf('Group with id "%s" is not found.', $groupId));
        }

        $user = $this->userRepository->find($userId);
        if ($user === null) {
            throw new UserMissingException(sprintf('User with id "%s" is not found.', $userId));
        }

        $group->removeMembership($user);
        $this->groupRepository->save($group);
    }
}
