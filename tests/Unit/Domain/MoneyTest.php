<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
#[CoversClass(Money::class)]
#[CoversClass(CurrencyMismatchException::class)]
#[CoversClass(NegativeAmountException::class)]
#[CoversClass(UnknownCurrencyException::class)]
final class MoneyTest extends TestCase
{
    /**
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCanBeCreatedAnAmountAndACurrency(): void
    {
        $money = Money::from(199, 'EUR');

        $this->assertSame(Currency::EUR, $money->currency);
        $this->assertSame(199, $money->amount);
    }

    /**
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCanBeCreatedFromAZeroAmountAndACurrency(): void
    {
        $money = Money::from(0, 'CHF');

        $this->assertSame(Currency::CHF, $money->currency);
        $this->assertSame(0, $money->amount);
    }

    /**
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCannotBeCreatedFromANegativeAmount(): void
    {
        $this->expectException(NegativeAmountException::class);
        $this->expectExceptionMessageIs('Amount must be a non-negative value, -489 given.');

        Money::from(-489, 'CHF');
    }

    /**
     * @throws CurrencyMismatchException
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCanAddMoneyWithEqualCurrency(): void
    {
        $moneyA = Money::from(2799, 'GBP');
        $moneyB = Money::from(1349, 'GBP');

        $moneySum = $moneyA->add($moneyB);

        $this->assertSame(4148, $moneySum->amount);
        $this->assertSame(Currency::GBP, $moneySum->currency);
        $this->assertNotSame($moneySum, $moneyA);
        $this->assertNotSame($moneySum, $moneyB);
    }

    /**
     * @throws CurrencyMismatchException
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCanAddMoneyWithNegativeAmount(): void
    {
        $moneyA = Money::from(2799, 'GBP');
        $moneyB = Money::from(0, 'GBP')->subtract(Money::from(9999, 'GBP'));

        $moneySum = $moneyA->add($moneyB);

        $this->assertSame(-7200, $moneySum->amount);
        $this->assertSame(Currency::GBP, $moneySum->currency);
    }

    /**
     * @throws CurrencyMismatchException
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCannotAddMoneyWithDifferentCurrency(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        $this->expectExceptionMessageIs('Currencies must match, "GBP" and "EUR" provided.');

        $moneyA = Money::from(2799, 'GBP');
        $moneyB = Money::from(1349, 'EUR');

        $moneyA->add($moneyB);
    }

    /**
     * @throws CurrencyMismatchException
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCanSubtractMoneyWithEqualCurrency(): void
    {
        $moneyA = Money::from(2799, 'GBP');
        $moneyB = Money::from(1349, 'GBP');

        $moneyDiff = $moneyA->subtract($moneyB);

        $this->assertSame(1450, $moneyDiff->amount);
        $this->assertSame(Currency::GBP, $moneyDiff->currency);
        $this->assertNotSame($moneyDiff, $moneyA);
        $this->assertNotSame($moneyDiff, $moneyB);
    }

    /**
     * @throws CurrencyMismatchException
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCannotSubtractMoneyWithDifferentCurrency(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        $this->expectExceptionMessageIs('Currencies must match, "GBP" and "EUR" provided.');

        $moneyA = Money::from(2799, 'GBP');
        $moneyB = Money::from(1349, 'EUR');

        $moneyA->subtract($moneyB);
    }

    /**
     * @throws CurrencyMismatchException
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public function testCanSubtractMoneyWithGreaterAmount(): void
    {
        $moneyA = Money::from(1999, 'GBP');
        $moneyB = Money::from(3499, 'GBP');

        $moneyDiff = $moneyA->subtract($moneyB);

        $this->assertSame(-1500, $moneyDiff->amount);
        $this->assertSame(Currency::GBP, $moneyDiff->currency);
    }
}
