<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

/**
 * The currencies supported by this system.
 *
 * Each case is backed by its ISO 4217 three-letter code.
 */
enum Currency: string
{
    /**
     * @throws UnknownCurrencyException
     */
    public static function fromCode(string $code): self
    {
        return self::tryFrom($code) ?? throw UnknownCurrencyException::from($code);
    }

    case EUR = 'EUR';

    case GBP = 'GBP';
}
