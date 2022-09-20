<?php

declare(strict_types=1);

namespace App\Application\Handler\Group\Dto;

use App\Domain\Group\Group;
use DateTime;
use DateTimeImmutable;

class GroupResponse
{
    private int $id;
    private string $title;
    private string $description;
    private DateTimeImmutable $createdAt;

    private function __construct()
    {
    }

    /**
     * @return array{'id': int, 'title': string, 'description': string, 'created_at': string}
     */
    public function __toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->createdAt->format(DateTime::ATOM),
        ];
    }

    public static function createFromGroup(Group $group): self
    {
        $object = new self();

        $object->id = $group->getId();
        $object->title = $group->title();
        $object->description = $group->description();
        $object->createdAt = $group->createdAt();

        return $object;
    }
}
