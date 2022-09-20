<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class GroupRepository implements GroupRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?Group
    {
        return $this->em->find(Group::class, $id);
    }

    public function save(Group $group): void
    {
        $this->em->persist($group);
        $this->em->flush();
    }

    public function remove(Group $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }

    /**
     * @return Collection<int, Group>
     */
    public function all(int $offset = 0, int $limit = 20): Collection
    {
        $query = $this->em->createQueryBuilder()
            ->select('g')
            ->from(Group::class, 'g')
            ->orderBy('g.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        $paginator = new Paginator($query, false);
        $groups = new ArrayCollection();

        foreach ($paginator as $group) {
            $groups->add($group);
        }

        return $groups;
    }
}
