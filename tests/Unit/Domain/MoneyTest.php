<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use const PHP_INT_MAX;
use const PHP_INT_MIN;
use function sprintf;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Money::class)]
#[CoversClass(Currency::class)]
#[CoversClass(UnknownCurrencyException::class)]
#[CoversClass(InvalidMoneyAmountException::class)]
#[CoversClass(CurrencyMismatchException::class)]
#[CoversClass(MoneyOverflowException::class)]
final class MoneyTest extends TestCase
{
    /**
     * @return Iterator<string, array{int}>
     */
    public static function negativeAmounts(): Iterator
    {
        yield 'negative cent' => [-1];

        yield 'negative amount' => [-100];

        yield 'minimum integer' => [PHP_INT_MIN];
    }

    public function testStoresAmountInCentsAndCurrency(): void
    {
        $money = Money::from(1234, 'EUR');

        $this->assertSame(1234, $money->amount());
        $this->assertTrue($money->currency()->equals(Currency::fromString('EUR')));
        $this->assertSame('GBP', Money::from(100, 'GBP')->currency()->asString());
    }

    public function testAcceptsZeroAndMaximumAmount(): void
    {
        $this->assertSame(0, Money::from(0, 'EUR')->amount());
        $this->assertSame(PHP_INT_MAX, Money::from(PHP_INT_MAX, 'EUR')->amount());
    }

    #[DataProvider('negativeAmounts')]
    public function testRejectsNegativeInitialAmounts(int $amount): void
    {
        $this->expectException(InvalidMoneyAmountException::class);
        $this->expectExceptionMessageIs(sprintf('The initial money amount must not be negative: %d', $amount));

        Money::from($amount, 'EUR');
    }

    public function testRejectsUnknownCurrency(): void
    {
        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessageIs('"USD" is not a supported currency');

        Money::from(100, 'USD');
    }

    public function testAddsWithoutChangingEitherOperand(): void
    {
        $money = Money::from(100, 'GBP');
        $other = Money::from(250, 'GBP');
        $sum   = $money->add($other);

        $this->assertSame(350, $sum->amount());
        $this->assertSame('GBP', $sum->currency()->asString());
        $this->assertNotSame($money, $sum);
        $this->assertNotSame($other, $sum);
        $this->assertSame(100, $money->amount());
        $this->assertSame(250, $other->amount());
        $this->assertNotSame($money, $money->add(Money::from(0, 'GBP')));
    }

    public function testSubtractsWithoutChangingEitherOperandAndAllowsRefunds(): void
    {
        $money      = Money::from(100, 'GBP');
        $other      = Money::from(250, 'GBP');
        $difference = $money->subtract($other);

        $this->assertSame(-150, $difference->amount());
        $this->assertSame('GBP', $difference->currency()->asString());
        $this->assertNotSame($money, $difference);
        $this->assertNotSame($other, $difference);
        $this->assertSame(100, $money->amount());
        $this->assertSame(250, $other->amount());
        $this->assertSame(150, $other->subtract($money)->amount());
        $this->assertSame(0, $money->subtract($money)->amount());
        $this->assertNotSame($money, $money->subtract(Money::from(0, 'GBP')));
        $this->assertSame(-50, $difference->add($money)->amount());
        $this->assertSame(-250, $difference->subtract($money)->amount());
        $this->assertSame(250, $money->subtract($difference)->amount());
    }

    public function testCannotAddDifferentCurrencies(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        $this->expectExceptionMessageIs('Currencies do not match: EUR and GBP');

        Money::from(100, 'EUR')->add(Money::from(100, 'GBP'));
    }

    public function testCannotSubtractDifferentCurrencies(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        $this->expectExceptionMessageIs('Currencies do not match: GBP and EUR');

        Money::from(0, 'GBP')->subtract(Money::from(0, 'EUR'));
    }

    public function testComparesBothAmountAndCurrency(): void
    {
        $money = Money::from(100, 'EUR');

        $this->assertTrue($money->equals(Money::from(100, 'EUR')));
        $this->assertFalse($money->equals(Money::from(101, 'EUR')));
        $this->assertFalse($money->equals(Money::from(100, 'GBP')));
        $this->assertFalse($money->equals(Money::from(101, 'GBP')));
    }

    public function testAllowsArithmeticAtIntegerLimits(): void
    {
        $money   = Money::from(PHP_INT_MAX, 'EUR');
        $one     = Money::from(1, 'EUR');
        $minimum = Money::from(0, 'EUR')->subtract($money)->subtract($one);

        $this->assertSame(PHP_INT_MAX, $money->add(Money::from(0, 'EUR'))->amount());
        $this->assertSame(PHP_INT_MIN, $minimum->amount());
        $this->assertSame(PHP_INT_MIN, $minimum->subtract(Money::from(0, 'EUR'))->amount());
        $this->assertSame(PHP_INT_MIN + 1, $minimum->add($one)->amount());
    }

    public function testRejectsAdditionOverflow(): void
    {
        $this->expectException(MoneyOverflowException::class);
        $this->expectExceptionMessageIs('The money amount exceeds the integer range');

        Money::from(PHP_INT_MAX, 'EUR')->add(Money::from(1, 'EUR'));
    }

    public function testRejectsAdditionUnderflow(): void
    {
        $money = Money::from(0, 'EUR')->subtract(Money::from(PHP_INT_MAX, 'EUR'));

        $this->expectException(MoneyOverflowException::class);
        $this->expectExceptionMessageIs('The money amount exceeds the integer range');

        $money->add($money);
    }

    public function testRejectsSubtractionUnderflow(): void
    {
        $money = Money::from(0, 'EUR')->subtract(Money::from(PHP_INT_MAX, 'EUR'));

        $this->expectException(MoneyOverflowException::class);
        $this->expectExceptionMessageIs('The money amount exceeds the integer range');

        $money->subtract(Money::from(2, 'EUR'));
    }

    public function testRejectsSubtractionOverflow(): void
    {
        $money = Money::from(0, 'EUR')->subtract(Money::from(1, 'EUR'));

        $this->expectException(MoneyOverflowException::class);
        $this->expectExceptionMessageIs('The money amount exceeds the integer range');

        Money::from(PHP_INT_MAX, 'EUR')->subtract($money);
    }
}
