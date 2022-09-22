<?php

declare(strict_types=1);

namespace App\Application\Response;

interface ResponseInterface
{
    /**
     * @return array<array-key,mixed>
     */
    public function data(): array;
}
