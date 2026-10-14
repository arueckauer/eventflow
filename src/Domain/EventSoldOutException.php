<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class EventSoldOutException extends PhpException implements Exception
{
    public static function withCapacity(int $capacity): self
    {
        return new self(
            sprintf('The event is sold out; all %d seats are reserved', $capacity),
        );
    }
}
