<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class UnknownReservationException extends PhpException implements Exception
{
    public static function for(SeatNumber $seatNumber): self
    {
        return new self(
            sprintf('Seat %s is not reserved', $seatNumber->asString()),
        );
    }
}
