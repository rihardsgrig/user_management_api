<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\User\User;
use DateTime;

class ListMembersHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository
    ) {
    }

    public function handle(int $groupId): array
    {
        $group = $this->groupRepository->find($groupId);
        if ($group === null) {
            throw new ResourceNotFoundException(sprintf('Group with id "%s" is not found.', $groupId));
        }

        return $group->memberships()->map(
            static function (User $user) {
                return [
                    'id' => $user->getId(),
                    'first_name' => $user->firstName(),
                    'last_name' => $user->lastName(),
                    'email' => $user->email(),
                    'is_admin' => $user->isAdmin(),
                    'created_at' => $user->createdAt()->format(DateTime::ATOM),
                ];
            }
        )->toArray();
    }
}
