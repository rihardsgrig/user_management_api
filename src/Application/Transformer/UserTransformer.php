<?php

declare(strict_types=1);

namespace App\Application\Transformer;

use App\Domain\User\User;
use DateTime;

class UserTransformer
{
    /**
     * @return array{
     *     'id': int,
     *     'first_name': string,
     *     'last_name': string,
     *     'email': string,
     *     'is_admin': bool,
     *     'created_at': string
     * }
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->getId(),
            'first_name' => $user->firstName(),
            'last_name' => $user->lastName(),
            'email' => $user->email(),
            'is_admin' => $user->isAdmin(),
            'created_at' => $user->createdAt()->format(DateTime::ATOM),
        ];
    }
}
