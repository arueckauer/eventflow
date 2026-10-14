<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function array_key_exists;
use function count;
use DateTimeImmutable;

/**
 * An event for which seats can be reserved.
 *
 * The event is the aggregate root that guards the reservation invariants:
 *
 *  - A seat can be reserved only once.
 *  - No more seats than the event's capacity can be reserved.
 *  - Once the event has started, no reservation can be made or cancelled.
 *
 * The current point in time is passed into the time-sensitive operations
 * instead of being read from a clock, so that the aggregate stays pure and its
 * behaviour is fully deterministic.
 */
final class Event
{
    use RecordsDomainEvents;

    /**
     * @var array<string, Reservation>
     */
    private array $reservations = [];

    /**
     * @throws InvalidCapacityException
     */
    public static function create(DateTimeImmutable $startsAt, int $capacity): self
    {
        if ($capacity < 1) {
            throw InvalidCapacityException::from($capacity);
        }

        return new self($startsAt, $capacity);
    }

    private function __construct(
        private readonly DateTimeImmutable $startsAt,
        private readonly int $capacity,
    ) {
    }

    public function startsAt(): DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function capacity(): int
    {
        return $this->capacity;
    }

    public function isReserved(SeatNumber $seatNumber): bool
    {
        return array_key_exists($seatNumber->asString(), $this->reservations);
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws EventSoldOutException
     * @throws SeatAlreadyTakenException
     */
    public function reserve(SeatNumber $seatNumber, Attendee $attendee, DateTimeImmutable $now): Reservation
    {
        if ($this->hasStarted($now)) {
            throw EventAlreadyStartedException::cannotReserve();
        }

        if ($this->isReserved($seatNumber)) {
            throw SeatAlreadyTakenException::for($seatNumber);
        }

        if (count($this->reservations) === $this->capacity) {
            throw EventSoldOutException::withCapacity($this->capacity);
        }

        $reservation = Reservation::for($seatNumber, $attendee);

        $this->reservations[$seatNumber->asString()] = $reservation;

        $this->recordThat(SeatReserved::for($seatNumber, $attendee));

        return $reservation;
    }

    /**
     * @throws EventAlreadyStartedException
     * @throws UnknownReservationException
     */
    public function cancelReservation(SeatNumber $seatNumber, DateTimeImmutable $now): void
    {
        if ($this->hasStarted($now)) {
            throw EventAlreadyStartedException::cannotCancel();
        }

        if (!$this->isReserved($seatNumber)) {
            throw UnknownReservationException::for($seatNumber);
        }

        unset($this->reservations[$seatNumber->asString()]);
    }

    private function hasStarted(DateTimeImmutable $now): bool
    {
        return $now >= $this->startsAt;
    }
}
