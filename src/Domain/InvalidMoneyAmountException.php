<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class InvalidMoneyAmountException extends PhpException implements Exception
{
    public static function from(int $amount): self
    {
        return new self(
            sprintf('The initial money amount must not be negative: %d', $amount),
        );
    }
}
