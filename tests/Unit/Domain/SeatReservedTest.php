<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(SeatReserved::class)]
#[CoversClass(SeatNumber::class)]
#[CoversClass(Attendee::class)]
#[TestDox('SeatReserved')]
final class SeatReservedTest extends TestCase
{
    public function testExposesTheReservedSeat(): void
    {
        $seatNumber = SeatNumber::fromString('A1');

        $this->assertTrue(
            SeatReserved::for($seatNumber, Attendee::named('Alice'))->seat()->equals($seatNumber),
        );
    }

    public function testExposesTheAttendee(): void
    {
        $attendee = Attendee::named('Alice');

        $this->assertTrue(
            SeatReserved::for(SeatNumber::fromString('A1'), $attendee)->attendee()->equals($attendee),
        );
    }
}
