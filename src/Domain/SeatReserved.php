<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

/**
 * Records that a seat of an event has been reserved for an attendee.
 */
final readonly class SeatReserved implements DomainEvent
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
