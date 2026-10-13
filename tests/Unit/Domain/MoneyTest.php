<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Money::class)]
#[CoversClass(Currency::class)]
#[CoversClass(CurrencyMismatchException::class)]
#[CoversClass(NegativeAmountException::class)]
#[CoversClass(UnknownCurrencyException::class)]
final class MoneyTest extends TestCase
{
    public function testCanBeCreatedFromAnAmountAndACurrency(): void
    {
        $money = Money::from(1234, 'EUR');

        $this->assertSame(1234, $money->amount());
        $this->assertSame(Currency::EUR, $money->currency());
    }

    public function testCanBeCreatedForEachSupportedCurrency(): void
    {
        $this->assertSame(Currency::EUR, Money::from(100, 'EUR')->currency());
        $this->assertSame(Currency::GBP, Money::from(100, 'GBP')->currency());
    }

    public function testCanHaveAnAmountOfZero(): void
    {
        $this->assertSame(0, Money::from(0, 'EUR')->amount());
    }

    public function testCannotBeCreatedWithANegativeAmount(): void
    {
        $this->expectException(NegativeAmountException::class);
        $this->expectExceptionMessageIs('-1 is not a valid amount; the amount of a Money must not be negative');

        Money::from(-1, 'EUR');
    }

    public function testCannotBeCreatedWithAnUnsupportedCurrency(): void
    {
        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessageIs('"USD" is not a supported currency');

        Money::from(100, 'USD');
    }

    public function testAmountsInTheSameCurrencyCanBeAdded(): void
    {
        $money = Money::from(100, 'EUR')->add(Money::from(50, 'EUR'));

        $this->assertSame(150, $money->amount());
        $this->assertSame(Currency::EUR, $money->currency());
    }

    public function testAmountsInDifferentCurrenciesCannotBeAdded(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        $this->expectExceptionMessageIs('Cannot operate on EUR and GBP; the currencies must match');

        Money::from(100, 'EUR')->add(Money::from(50, 'GBP'));
    }

    public function testAmountsInTheSameCurrencyCanBeSubtracted(): void
    {
        $money = Money::from(100, 'EUR')->subtract(Money::from(30, 'EUR'));

        $this->assertSame(70, $money->amount());
        $this->assertSame(Currency::EUR, $money->currency());
    }

    public function testSubtractionMayResultInANegativeAmount(): void
    {
        $money = Money::from(30, 'EUR')->subtract(Money::from(100, 'EUR'));

        $this->assertSame(-70, $money->amount());
    }

    public function testAmountsInDifferentCurrenciesCannotBeSubtracted(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        $this->expectExceptionMessageIs('Cannot operate on GBP and EUR; the currencies must match');

        Money::from(100, 'GBP')->subtract(Money::from(30, 'EUR'));
    }

    public function testAdditionDoesNotModifyTheOperands(): void
    {
        $money = Money::from(100, 'EUR');
        $money->add(Money::from(50, 'EUR'));

        $this->assertSame(100, $money->amount());
    }

    public function testSubtractionDoesNotModifyTheOperands(): void
    {
        $money = Money::from(100, 'EUR');
        $money->subtract(Money::from(50, 'EUR'));

        $this->assertSame(100, $money->amount());
    }

    public function testIsEqualToAnotherMoneyWithTheSameAmountAndCurrency(): void
    {
        $this->assertTrue(
            Money::from(100, 'EUR')->equals(Money::from(100, 'EUR')),
        );
    }

    public function testIsNotEqualToAnotherMoneyWithADifferentAmount(): void
    {
        $this->assertFalse(
            Money::from(100, 'EUR')->equals(Money::from(200, 'EUR')),
        );
    }

    public function testIsNotEqualToAnotherMoneyInADifferentCurrency(): void
    {
        $this->assertFalse(
            Money::from(100, 'EUR')->equals(Money::from(100, 'GBP')),
        );
    }
}
