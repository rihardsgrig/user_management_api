<?php

declare(strict_types=1);

namespace App\Application\Response;

class CollectionResponse implements ResponseInterface
{
    /**
     * @var array<array-key,mixed>
     */
    private array $data = [];

    /**
     * @param array<array-key,mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }
}
