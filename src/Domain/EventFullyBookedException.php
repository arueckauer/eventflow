<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use Exception as PhpException;

final class EventFullyBookedException extends PhpException implements Exception
{
    public static function forReservation(): self
    {
        return new self('Event is already fully booked.');
    }
}
