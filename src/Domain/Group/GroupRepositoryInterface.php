<?php

declare(strict_types=1);

namespace App\Domain\Group;

interface GroupRepositoryInterface
{
    public function save(Group $group): void;
}
