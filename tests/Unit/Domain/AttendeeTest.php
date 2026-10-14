<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Attendee::class)]
#[CoversClass(NameMustNotBeEmptyException::class)]
final class AttendeeTest extends TestCase
{
    public function testCannotCreateAttendeeFromEmptyString(): void
    {
        $this->expectException(NameMustNotBeEmptyException::class);

        Attendee::named('');
    }
}
