<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use ValueError;

enum Currency: string
{
    /**
     * @throws UnknownCurrencyException
     */
    public static function fromCode(string $code): self
    {
        try {
            return self::from($code);
        } catch (ValueError) {
            throw UnknownCurrencyException::fromCode($code);
        }
    }

    case CHF = 'CHF';
    case EUR = 'EUR';
    case GBP = 'GBP';
}
