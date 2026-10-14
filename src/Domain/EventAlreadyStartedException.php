<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use Exception as PhpException;

final class EventAlreadyStartedException extends PhpException implements Exception
{
    public static function forReservation(): self
    {
        return new self('Reservation only possible before event start.');
    }

    public static function forCancellation(): self
    {
        return new self('Cancellation only possible before event start.');
    }
}
