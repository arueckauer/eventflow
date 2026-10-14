<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use DateTimeImmutable;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Event::class)]
#[CoversClass(Reservation::class)]
#[CoversClass(Attendee::class)]
#[CoversClass(SeatNumber::class)]
#[CoversClass(SeatReserved::class)]
#[CoversTrait(RecordsDomainEvents::class)]
#[CoversClass(EventAlreadyStartedException::class)]
#[CoversClass(EventSoldOutException::class)]
#[CoversClass(InvalidCapacityException::class)]
#[CoversClass(SeatAlreadyTakenException::class)]
#[CoversClass(UnknownReservationException::class)]
final class EventTest extends TestCase
{
    private const string STARTS_AT = '2026-06-23 20:00:00';
    private const string BEFORE    = '2026-06-23 19:59:59';

    /**
     * @return Iterator<string, array{int}>
     */
    public static function invalidCapacities(): Iterator
    {
        yield 'zero' => [0];

        yield 'negative' => [-1];
    }

    /**
     * @return Iterator<string, array{string}>
     */
    public static function startedTimes(): Iterator
    {
        yield 'exactly at the start' => ['2026-06-23 20:00:00'];

        yield 'after the start' => ['2026-06-23 20:00:01'];
    }

    public function testCanBeCreatedWithACapacity(): void
    {
        $startsAt = new DateTimeImmutable(self::STARTS_AT);

        $event = Event::create($startsAt, 100);

        $this->assertSame($startsAt, $event->startsAt());
        $this->assertSame(100, $event->capacity());
    }

    public function testCanBeCreatedWithACapacityOfOne(): void
    {
        $event = Event::create(new DateTimeImmutable(self::STARTS_AT), 1);

        $this->assertSame(1, $event->capacity());
    }

    #[DataProvider('invalidCapacities')]
    public function testCannotBeCreatedWithACapacityBelowOne(int $capacity): void
    {
        $this->expectException(InvalidCapacityException::class);
        $this->expectExceptionMessageIs(
            sprintf('%d is not a valid capacity; the capacity of an event must be at least 1', $capacity),
        );

        Event::create(new DateTimeImmutable(self::STARTS_AT), $capacity);
    }

    public function testReservesAFreeSeatForAnAttendee(): void
    {
        $seatNumber = SeatNumber::fromString('A1');
        $attendee   = Attendee::named('Alice');
        $event      = $this->event();

        $reservation = $event->reserve($seatNumber, $attendee, new DateTimeImmutable(self::BEFORE));

        $this->assertTrue($reservation->seat()->equals($seatNumber));
        $this->assertTrue($reservation->attendee()->equals($attendee));
        $this->assertTrue($event->isReserved($seatNumber));
        $this->assertFalse($event->isReserved(SeatNumber::fromString('B2')));
    }

    public function testPublishesASeatReservedEventWhenASeatIsReserved(): void
    {
        $seatNumber = SeatNumber::fromString('A1');
        $attendee   = Attendee::named('Alice');
        $event      = $this->event();

        $event->reserve($seatNumber, $attendee, new DateTimeImmutable(self::BEFORE));

        $this->assertEquals(
            [SeatReserved::for($seatNumber, $attendee)],
            $event->releaseRecordedEvents(),
        );
    }

    public function testReservesDifferentSeats(): void
    {
        $event = $this->event(2);
        $now   = new DateTimeImmutable(self::BEFORE);

        $event->reserve(SeatNumber::fromString('A1'), $this->attendee(), $now);
        $event->reserve(SeatNumber::fromString('A2'), $this->attendee(), $now);

        $this->assertTrue($event->isReserved(SeatNumber::fromString('A1')));
        $this->assertTrue($event->isReserved(SeatNumber::fromString('A2')));
    }

    public function testCannotReserveASeatThatIsAlreadyTaken(): void
    {
        $event      = $this->event();
        $now        = new DateTimeImmutable(self::BEFORE);
        $seatNumber = SeatNumber::fromString('A1');

        $event->reserve($seatNumber, $this->attendee(), $now);

        $this->expectException(SeatAlreadyTakenException::class);
        $this->expectExceptionMessageIs('Seat A1 has already been reserved');

        $event->reserve($seatNumber, $this->attendee(), $now);
    }

    public function testCannotReserveBeyondTheCapacity(): void
    {
        $event = $this->event(2);
        $now   = new DateTimeImmutable(self::BEFORE);

        $event->reserve(SeatNumber::fromString('A1'), $this->attendee(), $now);
        $event->reserve(SeatNumber::fromString('A2'), $this->attendee(), $now);

        $this->expectException(EventSoldOutException::class);
        $this->expectExceptionMessageIs('The event is sold out; all 2 seats are reserved');

        $event->reserve(SeatNumber::fromString('A3'), $this->attendee(), $now);
    }

    #[DataProvider('startedTimes')]
    public function testCannotReserveAfterTheEventHasStarted(string $now): void
    {
        $event = $this->event();

        $this->expectException(EventAlreadyStartedException::class);
        $this->expectExceptionMessageIs('The event has already started; no new reservations can be made');

        $event->reserve(SeatNumber::fromString('A1'), $this->attendee(), new DateTimeImmutable($now));
    }

    public function testCancellingAReservationFreesTheSeat(): void
    {
        $event      = $this->event();
        $now        = new DateTimeImmutable(self::BEFORE);
        $seatNumber = SeatNumber::fromString('A1');

        $event->reserve($seatNumber, $this->attendee(), $now);
        $event->cancelReservation($seatNumber, $now);

        $this->assertFalse($event->isReserved($seatNumber));

        $event->reserve($seatNumber, $this->attendee(), $now);

        $this->assertTrue($event->isReserved($seatNumber));
    }

    public function testCannotCancelAReservationThatDoesNotExist(): void
    {
        $event = $this->event();

        $this->expectException(UnknownReservationException::class);
        $this->expectExceptionMessageIs('Seat A1 is not reserved');

        $event->cancelReservation(SeatNumber::fromString('A1'), new DateTimeImmutable(self::BEFORE));
    }

    public function testCannotCancelAReservationAfterTheEventHasStarted(): void
    {
        $event      = $this->event();
        $seatNumber = SeatNumber::fromString('A1');

        $event->reserve($seatNumber, $this->attendee(), new DateTimeImmutable(self::BEFORE));

        $this->expectException(EventAlreadyStartedException::class);
        $this->expectExceptionMessageIs('The event has already started; reservations can no longer be cancelled');

        $event->cancelReservation($seatNumber, new DateTimeImmutable(self::STARTS_AT));
    }

    private function event(int $capacity = 100): Event
    {
        return Event::create(new DateTimeImmutable(self::STARTS_AT), $capacity);
    }

    private function attendee(): Attendee
    {
        return Attendee::named('Alice');
    }
}
