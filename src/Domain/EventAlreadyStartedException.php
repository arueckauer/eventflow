<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use Exception as PhpException;

final class EventAlreadyStartedException extends PhpException implements Exception
{
    public static function cannotReserve(): self
    {
        return new self('The event has already started; no new reservations can be made');
    }

    public static function cannotCancel(): self
    {
        return new self('The event has already started; reservations can no longer be cancelled');
    }
}
