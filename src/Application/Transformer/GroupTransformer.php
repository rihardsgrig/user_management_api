<?php

declare(strict_types=1);

namespace App\Application\Transformer;

use App\Domain\Group\Group;
use DateTime;

class GroupTransformer
{
    /**
     * @return array{
     *     'id': int,
     *     'title': string,
     *     'description': string,
     *     'created_at': string
     * }
     */
    public function transform(Group $group): array
    {
        return [
            'id' => $group->getId(),
            'title' => $group->title(),
            'description' => $group->description(),
            'created_at' => $group->createdAt()->format(DateTime::ATOM),
        ];
    }
}
