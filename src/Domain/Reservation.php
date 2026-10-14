<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

/**
 * Books a single seat of an event for an attendee.
 *
 * Within an event a seat can be reserved only once, so the seat uniquely
 * identifies a reservation. A reservation is created by the Event aggregate and
 * is immutable.
 */
final readonly class Reservation
{
    public static function for(SeatNumber $seatNumber, Attendee $attendee): self
    {
        return new self($seatNumber, $attendee);
    }

    private function __construct(
        private SeatNumber $seatNumber,
        private Attendee $attendee,
    ) {
    }

    public function seat(): SeatNumber
    {
        return $this->seatNumber;
    }

    public function attendee(): Attendee
    {
        return $this->attendee;
    }
}
