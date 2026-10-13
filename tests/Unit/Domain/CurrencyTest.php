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
     * @return Iterator<string, array{string}>
     */
    public static function validCurrencyCodes(): Iterator
    {
        yield 'CHF' => ['CHF'];

        yield 'EUR' => ['EUR'];

        yield 'GBP' => ['GBP'];
    }

    /**
     * @return Iterator<string, array{string}>
     */
    public static function unknownCurrencyCodes(): Iterator
    {
        yield 'GBP' => ['DM'];

        yield 'EUR' => ['JPY'];

        yield 'CHF' => ['USD'];
    }

    /**
     * @throws UnknownCurrencyException
     */
    #[DataProvider('validCurrencyCodes')]
    #[TestDox('Supports the currency $code')]
    public function testSupportedCurrencies(string $code): void
    {
        $currency = Currency::fromCode($code);

        $this->assertSame($code, $currency->value);
    }

    #[DataProvider('unknownCurrencyCodes')]
    public function testFromCodeThrowsException(string $code): void
    {
        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessageIs(sprintf('Currency code "%s" is not supported.', $code));

        Currency::fromCode($code);
    }
}
