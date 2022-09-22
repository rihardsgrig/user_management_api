<?php

declare(strict_types=1);

namespace App\Application\Response;

class EmptyResponse implements ResponseInterface
{
    public function data(): array
    {
        return [];
    }
}
