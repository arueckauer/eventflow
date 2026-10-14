<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use DateTimeImmutable;

/**
 * Records seat reservations and permits cancellations only before the start.
 */
final class Event
{
    use RecordsDomainEvents;

    /**
     * @var array<string, SeatNumber>
     */
    private array $reservedSeats = [];

    public static function create(DateTimeImmutable $startsAt, int $capacity): self
    {
        return new self($startsAt, $capacity);
    }

    private function __construct(
        public readonly DateTimeImmutable $startsAt,
        public readonly int $capacity,
    ) {
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws SeatAlreadyTakenException
     */
    public function reserve(SeatNumber $seatNumber, DateTimeImmutable $now): void
    {
        if ($now >= $this->startsAt) {
            throw EventAlreadyStartedException::forReservation();
        }

        $key = $seatNumber->asString();

        if (isset($this->reservedSeats[$key])) {
            throw SeatAlreadyTakenException::from($seatNumber);
        }

        $this->reservedSeats[$key] = $seatNumber;

        $this->recordThat(new SeatReserved($seatNumber, $now));
    }

    /**
     * @throws EventAlreadyStartedException
     */
    public function cancelReservation(SeatNumber $seatNumber, DateTimeImmutable $now): void
    {
        if ($now >= $this->startsAt) {
            throw EventAlreadyStartedException::forCancellation();
        }

        unset($this->reservedSeats[$seatNumber->asString()]);
    }
}
