<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Response\CollectionResponse;
use App\Application\Response\EmptyResponse;
use App\Application\Response\ItemResponse;
use App\Application\Response\ResponseInterface;
use App\Application\Transformer\UserTransformer;
use App\Domain\User\User;

class UserResponseBuilder
{
    public function buildItem(User $user): ResponseInterface
    {
        return new ItemResponse(
            (new UserTransformer())->transform($user)
        );
    }

    /**
     * @param array<int, User> $users
     */
    public function buildCollection(iterable $users): ResponseInterface
    {
        $response = [];

        foreach ($users as $user) {
            $response[] = (new UserTransformer())->transform($user);
        }

        return new CollectionResponse($response);
    }

    public function buildEmpty(): ResponseInterface
    {
        return new EmptyResponse();
    }
}
