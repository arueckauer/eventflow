<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class NegativeAmountException extends PhpException implements Exception
{
    public static function from(int $amount): self
    {
        return new self(
            sprintf('%d is not a valid amount; the amount of a Money must not be negative', $amount),
        );
    }
}
