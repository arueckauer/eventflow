<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

final readonly class Attendee
{
    public static function named(string $name): self
    {
        if ($name === '') {
            throw NameMustNotBeEmptyException::create();
        }

        return new self($name);
    }

    private function __construct(
        public string $name,
    ) {
    }
}
