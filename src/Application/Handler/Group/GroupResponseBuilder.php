<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Response\CollectionResponse;
use App\Application\Response\EmptyResponse;
use App\Application\Response\ItemResponse;
use App\Application\Response\ResponseInterface;
use App\Application\Transformer\GroupTransformer;
use App\Domain\Group\Group;

class GroupResponseBuilder
{
    public function buildItem(Group $group): ResponseInterface
    {
        return new ItemResponse(
            (new GroupTransformer())->transform($group)
        );
    }

    /**
     * @param array<int, Group> $groups
     */
    public function buildCollection(iterable $groups): ResponseInterface
    {
        $response = [];

        foreach ($groups as $group) {
            $response[] = (new GroupTransformer())->transform($group);
        }

        return new CollectionResponse($response);
    }

    public function buildEmpty(): ResponseInterface
    {
        return new EmptyResponse();
    }
}
