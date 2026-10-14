<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

final readonly class Reservation
{
    public function __construct(
        public SeatNumber $seatNumber,
        public Attendee $attendee,
    ) {
    }
}
