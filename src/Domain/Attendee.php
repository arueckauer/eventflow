<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

/**
 * Identifies the person a reservation is made for.
 *
 * An attendee is identified by their name, which must not be empty.
 */
final readonly class Attendee
{
    /**
     * @throws InvalidAttendeeException
     */
    public static function named(string $name): self
    {
        if ($name === '') {
            throw InvalidAttendeeException::from($name);
        }

        return new self($name);
    }

    private function __construct(
        private string $name,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->name;
    }
}
