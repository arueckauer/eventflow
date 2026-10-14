<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
#[CoversClass(UnknownCurrencyException::class)]
final class CurrencyTest extends TestCase
{
    /**
     * @return Iterator<string, array{string}>
     */
    public static function unknownCodes(): Iterator
    {
        yield 'empty' => [''];

        yield 'unsupported' => ['USD'];

        yield 'lowercase' => ['eur'];

        yield 'whitespace' => [' EUR'];

        yield 'trailing newline' => ["EUR\n"];
    }

    public function testCanBeCreatedFromSupportedCodes(): void
    {
        $this->assertSame('EUR', Currency::fromString('EUR')->asString());
        $this->assertSame('GBP', Currency::fromString('GBP')->asString());
    }

    #[DataProvider('unknownCodes')]
    public function testRejectsUnknownCodes(string $code): void
    {
        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessageIs(sprintf('"%s" is not a supported currency', $code));

        Currency::fromString($code);
    }

    public function testComparesCurrenciesByCode(): void
    {
        $this->assertTrue(Currency::fromString('EUR')->equals(Currency::fromString('EUR')));
        $this->assertTrue(Currency::fromString('GBP')->equals(Currency::fromString('GBP')));
        $this->assertFalse(Currency::fromString('EUR')->equals(Currency::fromString('GBP')));
    }
}
