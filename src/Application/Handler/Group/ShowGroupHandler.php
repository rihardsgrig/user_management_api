<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Response\ResponseInterface;
use App\Domain\Group\GroupRepositoryInterface;

class ShowGroupHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly GroupResponseBuilder $responseBuilder
    ) {
    }

    public function handle(int $id): ResponseInterface
    {
        $group = $this->groupRepository->find($id);

        if ($group === null) {
            throw new ResourceNotFoundException(sprintf('Group with id "%s" is not found.', $id));
        }

        return $this->responseBuilder->buildItem($group);
    }
}
