<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventListener;

use App\EventListener\ApiResponse;
use PHPUnit\Framework\TestCase;

class ApiResponseTest extends TestCase
{
    public function testGroup(): void
    {
        $response = new ApiResponse(
            'test message',
            ['email' => ['Invalid format']],
            400,
            [],
            false
        );

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('{"message":"test message","errors":{"email":["Invalid format"]}}', $response->getContent());
    }
}
