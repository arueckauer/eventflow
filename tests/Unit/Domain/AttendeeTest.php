<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Attendee::class)]
#[CoversClass(InvalidAttendeeException::class)]
final class AttendeeTest extends TestCase
{
    public function testCanBeCreatedFromAName(): void
    {
        $this->assertSame('Alice', Attendee::named('Alice')->name());
    }

    public function testCannotBeCreatedFromAnEmptyName(): void
    {
        $this->expectException(InvalidAttendeeException::class);
        $this->expectExceptionMessageIs('"" is not a valid attendee name; the name must not be empty');

        Attendee::named('');
    }

    public function testIsEqualToAnotherAttendeeWithTheSameName(): void
    {
        $this->assertTrue(
            Attendee::named('Alice')->equals(Attendee::named('Alice')),
        );
    }

    public function testIsNotEqualToAnotherAttendeeWithADifferentName(): void
    {
        $this->assertFalse(
            Attendee::named('Alice')->equals(Attendee::named('Bob')),
        );
    }
}
