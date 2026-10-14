<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class InvalidAttendeeException extends PhpException implements Exception
{
    public static function from(string $name): self
    {
        return new self(
            sprintf('"%s" is not a valid attendee name; the name must not be empty', $name),
        );
    }
}
