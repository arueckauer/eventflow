<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class CurrencyMismatchException extends PhpException implements Exception
{
    public static function from(Currency $expected, Currency $actual): self
    {
        return new self(
            sprintf('Currencies do not match: %s and %s', $expected->asString(), $actual->asString()),
        );
    }
}
