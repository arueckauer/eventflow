<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
#[CoversClass(UnknownCurrencyException::class)]
final class CurrencyTest extends TestCase
{
    /**
     * @return Iterator<string, array{string, Currency}>
     */
    public static function supportedCurrencies(): Iterator
    {
        yield 'EUR' => ['EUR', Currency::EUR];

        yield 'GBP' => ['GBP', Currency::GBP];
    }

    /**
     * @return Iterator<string, array{string}>
     */
    public static function unsupportedCurrencies(): Iterator
    {
        yield 'USD' => ['USD'];

        yield 'CHF' => ['CHF'];

        yield 'empty string' => [''];

        yield 'lowercase code of a supported currency' => ['eur'];
    }

    #[DataProvider('supportedCurrencies')]
    #[TestDox('Supports the currency $code')]
    public function testIsSupported(string $code, Currency $currency): void
    {
        $this->assertSame($currency, Currency::fromCode($code));
    }

    #[DataProvider('unsupportedCurrencies')]
    #[TestDox('Rejects the unsupported currency $code')]
    public function testIsNotSupported(string $code): void
    {
        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessageIs(sprintf('"%s" is not a supported currency', $code));

        Currency::fromCode($code);
    }
}
