<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if ('json' === $request->getContentType()) {
            $response = $this->createApiResponse($exception);
            $event->setResponse($response);
        }
    }

    private function createApiResponse(Throwable $exception): ApiResponse
    {
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $errors = [];

        return new ApiResponse($exception->getMessage(), $errors, $statusCode);
    }
}
