<?php

declare(strict_types=1);

namespace App\Domain\Group;

use App\Domain\User\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

#[Entity()]
#[ORM\Table(name:"usergroup")]
class Group
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", options: ["unsigned"=>true])]
    private int $id;

    #[ORM\Column(type: "string", length:255)]
    private string $title;

    #[ORM\Column(type: "string")]
    private string $description;

    #[ORM\Column(type:"datetime_immutable", nullable:false , options:["default"=>"CURRENT_TIMESTAMP"])]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "groups", cascade: ['persist', 'merge'], fetch: 'LAZY')]
    private Collection $memberships;

    public function __construct(
        string $title,
        string $description
    ) {
        $this->setTitle($title);
        $this->setDescription($description);
        $this->memberships = new ArrayCollection();
        $this->setCreatedAt(new DateTimeImmutable());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, User>
     */
    public function memberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(User $member): void
    {
        if ($this->memberships->contains($member)) {
            return;
        }

        $this->memberships->add($member);
        $member->addGroup($this);
    }

    public function removeMembership(User $member): void
    {
        if (!$this->memberships->contains($member)) {
            return;
        }

        $this->memberships->removeElement($member);
        $member->removeGroup($this);
    }

    private function setTitle(string $title): void
    {
        $this->title = $title;
    }

    private function setDescription(string $description): void
    {
        $this->description = $description;
    }

    private function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
