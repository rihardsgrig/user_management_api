<?php

declare(strict_types=1);

namespace App\Application\Response;

class ItemResponse implements ResponseInterface
{
    /**
     * @var array<string,mixed>
     */
    private array $data = [];

    /**
     * @param array<string,mixed> $data
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
