<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use DateTime;

class ListGroupsHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository
    ) {
    }

    /**
     * @return array<int, array{title: string, description: string, created_at: string}>
     */
    public function handle(int $offset = 0, int $limit = 10): array
    {
        return $this->groupRepository->all($offset, $limit)->map(
            static function (Group $group) {
                return [
                    'id' => $group->getId(),
                    'title' => $group->title(),
                    'description' => $group->description(),
                    'created_at' => $group->createdAt()->format(DateTime::ATOM),
                ];
            }
        )->toArray();
    }
}
