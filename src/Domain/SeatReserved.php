<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use DateTimeImmutable;

final readonly class SeatReserved implements DomainEvent
{
    public function __construct(
        public SeatNumber $seatNumber,
        public Attendee $attendee,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
