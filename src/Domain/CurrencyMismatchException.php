<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class CurrencyMismatchException extends PhpException implements Exception
{
    public static function between(Currency $expected, Currency $actual): self
    {
        return new self(
            sprintf(
                'Cannot operate on %s and %s; the currencies must match',
                $expected->value,
                $actual->value,
            ),
        );
    }
}
