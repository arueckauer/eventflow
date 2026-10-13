<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class NegativeAmountException extends PhpException implements Exception
{
    public static function fromAmount(int $amount): self
    {
        return new self(sprintf('Amount must be a non-negative value, %d given.', $amount));
    }
}
