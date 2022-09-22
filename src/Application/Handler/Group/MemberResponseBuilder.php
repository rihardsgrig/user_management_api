<?php

declare(strict_types=1);

namespace App\Application\Handler\Group;

use App\Application\Response\CollectionResponse;
use App\Application\Response\EmptyResponse;
use App\Application\Response\ResponseInterface;
use App\Application\Transformer\MemberTransformer;
use App\Domain\User\User;

class MemberResponseBuilder
{
    /**
     * @param array<int, User> $members
     */
    public function buildCollection(iterable $members): ResponseInterface
    {
        $response = [];

        foreach ($members as $member) {
            $response[] = (new MemberTransformer())->transform($member);
        }

        return new CollectionResponse($response);
    }

    public function buildEmpty(): ResponseInterface
    {
        return new EmptyResponse();
    }
}
