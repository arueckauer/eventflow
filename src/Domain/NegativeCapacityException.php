<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class NegativeCapacityException extends PhpException implements Exception
{
    public static function fromCapacity(int $capacity): self
    {
        return new self(sprintf('Capacity must be a non-negative value, %d given.', $capacity));
    }
}
