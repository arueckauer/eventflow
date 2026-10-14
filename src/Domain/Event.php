<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function count;
use DateTimeImmutable;

/**
 * Records seat reservations and permits cancellations only before the start.
 */
final class Event
{
    use RecordsDomainEvents;

    /**
     * @var array<string, Reservation>
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
     * @throws EventFullyBookedException
     * @throws SeatAlreadyTakenException
     */
    public function reserve(SeatNumber $seatNumber, Attendee $attendee, DateTimeImmutable $now): Reservation
    {
        if ($now >= $this->startsAt) {
            throw EventAlreadyStartedException::forReservation();
        }

        if (count($this->reservedSeats) >= $this->capacity) {
            throw EventFullyBookedException::forReservation();
        }

        $key = $seatNumber->asString();

        if (isset($this->reservedSeats[$key])) {
            throw SeatAlreadyTakenException::from($seatNumber);
        }

        $reservation               = new Reservation($seatNumber, $attendee);
        $this->reservedSeats[$key] = $reservation;

        $this->recordThat(new SeatReserved($seatNumber, $attendee, $now));

        return $reservation;
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
