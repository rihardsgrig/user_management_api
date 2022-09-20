<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Group\Group;
use App\Domain\User\Exception\InvalidInputDataException;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

#[Entity()]
class User
{
    private const DEFAULT_USER_ROLE = 'ROLE_USER';
    private const ADMIN_ROLE = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 32, unique: true, nullable: false)]
    private string $email;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: 'json', nullable: false)]
    private array $roles = [];

    #[ORM\Column(type: 'datetime_immutable', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'memberships')]
    private Collection $groups;

    /**
     * @param array<int, string> $roles
     */
    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        UniqueEmailSpecificationInterface $uniqueEmailSpecification,
        array $roles = [self::DEFAULT_USER_ROLE]
    ) {
        if (!$uniqueEmailSpecification->isSatisfiedBy($email)) {
            throw new InvalidInputDataException(sprintf('Email %s already exists', $email));
        }

        $this->groups = new ArrayCollection();
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setRoles($roles);
        $this->setCreatedAt(new DateTimeImmutable());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isAdmin(): bool
    {
        return in_array(self::ADMIN_ROLE, $this->roles(), true);
    }

    /**
     * @return array<int, string>
     */
    public function roles(): array
    {
        return $this->roles;
    }

    public function addGroup(Group $group): void
    {
        if ($this->groups->contains($group)) {
            return;
        }

        $this->groups->add($group);
        $group->addMembership($this);
    }

    public function removeGroup(Group $group): void
    {
        if (!$this->groups->contains($group)) {
            return;
        }

        $this->groups->removeElement($group);
        $group->removeMembership($this);
    }

    private function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    private function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    private function setEmail(string $email): void
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email $email");
        }

        $this->email = $email;
    }

    private function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param array<int, string> $roles
     */
    private function setRoles(array $roles): void
    {
        if (!in_array(self::DEFAULT_USER_ROLE, $roles, true)) {
            $roles[] = self::DEFAULT_USER_ROLE;
        }

        $this->roles = array_unique($roles);
    }
}
