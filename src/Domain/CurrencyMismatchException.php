<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class CurrencyMismatchException extends PhpException implements Exception
{
    public static function from(Currency $currencyA, Currency $currencyB): self
    {
        return new self(sprintf('Currencies must match, "%s" and "%s" provided.', $currencyA->value, $currencyB->value));
    }
}
