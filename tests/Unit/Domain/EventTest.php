<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Attendee::class)]
#[CoversClass(Event::class)]
#[CoversClass(EventAlreadyStartedException::class)]
#[CoversClass(EventFullyBookedException::class)]
#[CoversClass(Reservation::class)]
#[CoversClass(SeatAlreadyTakenException::class)]
#[CoversClass(SeatNumber::class)]
#[CoversClass(SeatReserved::class)]
final class EventTest extends TestCase
{
    public function testCanCreateEventWithExpectedCapacity(): void
    {
        $startsAt = new DateTimeImmutable('2026-11-10 20:00:00');
        $capacity = 20;

        $event = Event::create($startsAt, $capacity);

        $this->assertSame($startsAt, $event->startsAt);
        $this->assertSame($capacity, $event->capacity);
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventFullyBookedException
     * @throws InvalidSeatNumberException
     * @throws SeatAlreadyTakenException
     */
    public function testCanReserveFreeSeat(): void
    {
        $this->expectNotToPerformAssertions();

        $event = Event::create(new DateTimeImmutable('2026-11-10 20:00:00'), 20);

        $event->reserve(SeatNumber::fromString('A5'), Attendee::named('Andi'), new DateTimeImmutable);
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventFullyBookedException
     * @throws InvalidSeatNumberException
     * @throws SeatAlreadyTakenException
     */
    public function testCannotReserveTakenSeat(): void
    {
        $event = Event::create(new DateTimeImmutable('2026-11-10 20:00:00'), 20);

        $seatNumber = SeatNumber::fromString('A5');
        $attendee   = Attendee::named('Andi');

        $event->reserve($seatNumber, $attendee, new DateTimeImmutable);

        $this->expectException(SeatAlreadyTakenException::class);
        $this->expectExceptionMessageIs('Seat "A5" is already taken.');

        $event->reserve($seatNumber, $attendee, new DateTimeImmutable);
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventFullyBookedException
     * @throws InvalidSeatNumberException
     * @throws SeatAlreadyTakenException
     */
    public function testCannotReserveAfterEventStart(): void
    {
        $event = Event::create(new DateTimeImmutable('2026-11-10 20:00:00'), 20);

        $this->expectException(EventAlreadyStartedException::class);
        $this->expectExceptionMessageIs('Reservation only possible before event start.');

        $event->reserve(SeatNumber::fromString('A5'), Attendee::named('Andi'), new DateTimeImmutable('2026-11-10 20:00:00'));
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventFullyBookedException
     * @throws InvalidSeatNumberException
     * @throws SeatAlreadyTakenException
     */
    public function testCannotReserveFullBookedEvent(): void
    {
        $event = Event::create(new DateTimeImmutable('2026-11-10 20:00:00'), 0);

        $this->expectException(EventFullyBookedException::class);
        $this->expectExceptionMessageIs('Event is already fully booked.');

        $event->reserve(SeatNumber::fromString('A5'), Attendee::named('Andi'), new DateTimeImmutable('2026-11-10 20:00:00'));
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventFullyBookedException
     * @throws InvalidSeatNumberException
     * @throws SeatAlreadyTakenException
     */
    public function testCancelingAReservationMakesTheReservedSeatAvailableAgain(): void
    {
        $this->expectNotToPerformAssertions();

        $event = Event::create(new DateTimeImmutable('2026-11-10 20:00:00'), 20);

        $seatNumber = SeatNumber::fromString('A5');
        $attendee   = Attendee::named('Andi');

        $event->reserve($seatNumber, $attendee, new DateTimeImmutable);

        $event->cancelReservation($seatNumber, new DateTimeImmutable);

        $event->reserve($seatNumber, $attendee, new DateTimeImmutable);
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventFullyBookedException
     * @throws InvalidSeatNumberException
     * @throws SeatAlreadyTakenException
     */
    public function testCannotCancelAfterEventStart(): void
    {
        $event = Event::create(new DateTimeImmutable('2026-11-10 20:00:00'), 20);

        $seatNumber = SeatNumber::fromString('A5');

        $event->reserve($seatNumber, Attendee::named('Andi'), new DateTimeImmutable);

        $this->expectException(EventAlreadyStartedException::class);
        $this->expectExceptionMessageIs('Cancellation only possible before event start.');

        $event->cancelReservation($seatNumber, new DateTimeImmutable('2026-11-10 20:00:00'));
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventFullyBookedException
     * @throws InvalidSeatNumberException
     * @throws SeatAlreadyTakenException
     */
    public function testCanRecordReservation(): void
    {
        $event = Event::create(new DateTimeImmutable('2026-11-10 20:00:00'), 20);

        $event->reserve(SeatNumber::fromString('A5'), Attendee::named('Andi'), new DateTimeImmutable);

        $recordedEvents = $event->releaseRecordedEvents();

        $this->assertCount(1, $recordedEvents);
        $this->assertInstanceOf(SeatReserved::class, $recordedEvents[0] ?? null);
    }
}
