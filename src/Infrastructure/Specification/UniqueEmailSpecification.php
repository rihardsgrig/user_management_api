<?php

declare(strict_types=1);

namespace App\Infrastructure\Specification;

use App\Domain\User\UniqueEmailSpecificationInterface;
use App\Domain\User\User;
use Doctrine\ORM\EntityManagerInterface;

class UniqueEmailSpecification implements UniqueEmailSpecificationInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function isSatisfiedBy(string $email): bool
    {
        return $this->em->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.email = :email')
                ->setParameters(['email' => $email])
                ->getQuery()->getOneOrNullResult() === null;
    }
}
