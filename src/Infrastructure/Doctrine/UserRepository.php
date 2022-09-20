<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function find(int $id): ?User
    {
        return $this->em->find(User::class, $id);
    }

    public function remove(User $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }

    /**
     * @return Collection<int, User>
     */
    public function all(int $offset = 0, int $limit = 10): Collection
    {
        $query = $this->em->createQueryBuilder()
            ->select('g')
            ->from(User::class, 'g')
            ->orderBy('g.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        $paginator = new Paginator($query, false);
        $users = new ArrayCollection();

        foreach ($paginator as $user) {
            $users->add($user);
        }

        return $users;
    }
}
