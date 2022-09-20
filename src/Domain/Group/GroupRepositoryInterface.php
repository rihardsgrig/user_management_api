<?php

declare(strict_types=1);

namespace App\Domain\Group;

use Doctrine\Common\Collections\Collection;

interface GroupRepositoryInterface
{
    public function find(int $id): ?Group;

    public function save(Group $group): void;

    public function remove(Group $task): void;

    /**
     * @return Collection<int, Group>
     */
    public function all(int $offset = 0, int $limit = 10): Collection;
}
