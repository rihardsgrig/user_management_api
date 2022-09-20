<?php

declare(strict_types=1);

namespace App\Domain\User;

use Doctrine\Common\Collections\Collection;

interface UserRepositoryInterface
{
    public function find(int $id): ?User;

    public function save(User $user): void;

    public function remove(User $task): void;

    /**
     * @return Collection<int, User>
     */
    public function all(int $offset = 0, int $limit = 10): Collection;
}
