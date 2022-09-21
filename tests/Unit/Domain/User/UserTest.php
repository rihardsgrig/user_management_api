<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User;

use App\Domain\User\Exception\InvalidInputDataException;
use App\Domain\User\UniqueEmailSpecificationInterface;
use App\Domain\User\User;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUser(): void
    {
        $uniqueEmailSpecification = $this->getMockBuilder(UniqueEmailSpecificationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uniqueEmailSpecification->method('isSatisfiedBy')
            ->willReturn(true);

        $user = new User(
            'first_name',
            'last_name',
            'test@test.com',
            $uniqueEmailSpecification
        );

        $this->assertEquals(['ROLE_USER'], $user->roles());
        $this->assertEquals('first_name', $user->firstName());
        $this->assertEquals('last_name', $user->lastName());
        $this->assertEquals('test@test.com', $user->email());
        $this->assertFalse($user->isAdmin());
    }

    public function testUserEmailIsNotValid(): void
    {
        $uniqueEmailSpecification = $this->getMockBuilder(UniqueEmailSpecificationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uniqueEmailSpecification->method('isSatisfiedBy')
            ->willReturn(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email "test".');

        new User(
            'first_name',
            'last_name',
            'test',
            $uniqueEmailSpecification
        );
    }

    public function testUserEmailIsNotUnique(): void
    {
        $uniqueEmailSpecification = $this->getMockBuilder(UniqueEmailSpecificationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uniqueEmailSpecification->method('isSatisfiedBy')
            ->willReturn(false);

        $this->expectException(InvalidInputDataException::class);
        $this->expectExceptionMessage('User with email "test@test.com" already exist.');

        new User(
            'first_name',
            'last_name',
            'test@test.com',
            $uniqueEmailSpecification
        );
    }

    public function testUserHasAdminRole(): void
    {
        $uniqueEmailSpecification = $this->getMockBuilder(UniqueEmailSpecificationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uniqueEmailSpecification->method('isSatisfiedBy')
            ->willReturn(true);

        $user = new User(
            'first_name',
            'last_name',
            'test@test.com',
            $uniqueEmailSpecification,
            ['ROLE_ADMIN', 'ROLE_USER']
        );

        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->roles());
        $this->assertTrue($user->isAdmin());
    }
}
