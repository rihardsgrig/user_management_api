<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Group;

use App\Domain\Group\Group;
use App\Domain\User\UniqueEmailSpecificationInterface;
use App\Domain\User\User;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    public function testGroup(): void
    {
        $group = new Group(
            'group_1',
            'some description'
        );

        $user = $this->user();
        $group->addMembership($user);

        $this->assertEquals('group_1', $group->title());
        $this->assertEquals('some description', $group->description());
        $this->assertTrue($group->hasMembers());
        $this->assertTrue($group->hasMember($user));
    }

    public function testMembershipIsRemoved(): void
    {
        $group = new Group(
            'group_1',
            'some description'
        );

        $user = $this->user();
        $group->addMembership($user);

        $group->removeMembership($user);
        $this->assertFalse($group->hasMembers());
        $this->assertFalse($group->hasMember($user));
    }

    private function user(): User
    {
        $uniqueEmailSpecification = $this->getMockBuilder(UniqueEmailSpecificationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uniqueEmailSpecification->method('isSatisfiedBy')
            ->willReturn(true);

        return new User(
            'first_name',
            'last_name',
            'test@test.com',
            $uniqueEmailSpecification
        );
    }
}
