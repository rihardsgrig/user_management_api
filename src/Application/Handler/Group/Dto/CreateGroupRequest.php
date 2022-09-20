<?php

declare(strict_types=1);

namespace App\Application\Handler\Group\Dto;

class CreateGroupRequest
{
    public string $title;
    public string $description;

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }
}
