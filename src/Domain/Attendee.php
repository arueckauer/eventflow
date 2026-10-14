<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

final readonly class Attendee
{
    public static function named(string $name): self
    {
        return new self($name);
    }

    private function __construct(
        public string $name,
    ) {
    }
}
