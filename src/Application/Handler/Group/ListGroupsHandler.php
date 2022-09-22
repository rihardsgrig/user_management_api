<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Response\ResponseInterface;
use App\Domain\Group\GroupRepositoryInterface;

class ListGroupsHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly GroupResponseBuilder $responseBuilder
    ) {
    }

    public function handle(int $offset = 0, int $limit = 10): ResponseInterface
    {
        return $this->responseBuilder->buildCollection(
            $this->groupRepository->all($offset, $limit)
        );
    }
}
