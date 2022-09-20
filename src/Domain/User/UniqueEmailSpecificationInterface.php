<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UniqueEmailSpecificationInterface
{
    public function isSatisfiedBy(string $email): bool;
}
