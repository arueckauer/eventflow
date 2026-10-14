<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class SeatAlreadyTakenException extends PhpException implements Exception
{
    public static function from(SeatNumber $seatNumber): self
    {
        return new self(sprintf('Seat "%s" is already taken.', $seatNumber->asString()));
    }
}
