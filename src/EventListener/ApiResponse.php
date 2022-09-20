<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * @param array<string, array<string>> $errors
     * @param array<string, string> $headers
     */
    public function __construct(string $message, array $errors = [], int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($this->format($message, $errors), $status, $headers, $json);
    }

    /**
     * @param array<string, array<string>> $errors
     *
     * @return array{message: string, errors?: non-empty-array<string, array<string>>}
     */
    private function format(string $message, array $errors = []): array
    {
        $response = [
            'message' => $message,
        ];

        if (0 !== count($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
