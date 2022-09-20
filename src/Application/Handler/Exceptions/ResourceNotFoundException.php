<?php

declare(strict_types=1);

namespace App\Application\Handler\Exceptions;

use RuntimeException;
use Throwable;

final class ResourceNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Resource not found.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
