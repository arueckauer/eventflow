<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class InvalidCapacityException extends PhpException implements Exception
{
    public static function from(int $capacity): self
    {
        return new self(
            sprintf('%d is not a valid capacity; the capacity of an event must be at least 1', $capacity),
        );
    }
}
